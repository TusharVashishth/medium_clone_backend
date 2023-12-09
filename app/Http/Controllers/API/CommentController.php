<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;
use Log;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $postId = $request->query("post_id");
            $comments = Comment::select("*")->where("post_id", $postId)->get();
            return ["status" => 200, "comments" => $comments];
        } catch (\Exception $err) {
            Log::info("comment_fetch_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "post_id" => "required",
            "content" => "required|max:10000"
        ]);
        try {
            $user = $request->user();
            $payload["user_id"] = $user->id;
            $comment = Comment::create($payload);
            return ["status" => 200, "message" => "Commented successfully!", "comment" => $comment];
        } catch (\Exception $err) {
            Log::info("comment_create_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $comment = Comment::find($id);
            if ($user->id != $comment->user_id) {
                return ["status" => 401, "message" => "UnAthorized"];
            }
            $comment->delete();
            return response()->json(["status" => 200, "message" => "Comment Deleted successfully!"]);
        } catch (\Exception $err) {
            Log::info("comment_delete_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }

    }
}
