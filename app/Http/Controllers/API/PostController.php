<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::select("id", "user_id", "title", "short_description", "image", "created_at")->with("user")->orderByDesc("id")->get();
        return ["status" => 200, "posts" => $posts];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "title" => "required|min:5|max:190",
            "content" => "nullable|max:20000",
            "short_description" => "min:5|max:250",
            "image" => "nullable|image|mimes:png,jpg,svg,webp,jpeg,gif|max:2048"
        ]);
        try {
            $user = $request->user();
            $payload["user_id"] = $user->id;

            if (isset($payload["image"])) {
                $payload["image"] = $payload["image"]->store($user->id);
            }

            Post::create($payload);
            return ["status" => 200, "message" => "Post created successfully!"];

        } catch (\Exception $err) {
            Log::info("post_create_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::select("id", "user_id", "title", "image", "content", "short_description", "created_at")->with("user")->where("id", $id)->first();
            return ["status" => 200, "post" => $post];
        } catch (\Exception $err) {
            Log::info("post_show_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payload = $request->validate([
            "title" => "required|min:5|max:190",
            "content" => "nullable|max:20000",
        ]);
        try {
            Post::where("id", $id)->update($payload);
            return ["status" => 200, "message" => "Post updated successfully!"];
        } catch (\Exception $err) {
            Log::info("post_show_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $post = Post::find($id);
            $user = $request->user();
            if ($post->user_id != $user->id) {
                return response()->json(["status" => 401, "message" => "UnAuthorized"]);
            }

            // * delete image 
            if ($post->image) {
                Storage::delete($post->image);
            }

            $post->delete();
            return response()->json(["status" => 200, "message" => "Post Deleted successfully!"]);
        } catch (\Exception $err) {
            Log::info("post_delete_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }

    }

    // * fetch users posts
    public function fetchUsersPost(Request $request)
    {
        $userId = $request->query("user_id");
        $posts = Post::select("id", "user_id", "title", "image", "created_at")->with("user")->where('user_id', $userId)->orderByDesc("id")->get();
        return ["status" => 200, "posts" => $posts];
    }
}
