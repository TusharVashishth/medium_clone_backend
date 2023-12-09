<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Log;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->query("query");
            $posts = Post::select("id", "user_id", "title", "image", "content", "short_description", "created_at")->with("user")->whereFullText("title", $query)->get();
            return ["status" => 200, "posts" => $posts];

        } catch (\Exception $err) {
            Log::info("search_show_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong.please try again!"], 500);
        }

    }
}
