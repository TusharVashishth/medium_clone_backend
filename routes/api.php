<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware("auth:sanctum")->group(function () {

    Route::get("/user/posts", [PostController::class, 'fetchUsersPost']);
    Route::apiResource("post", PostController::class)->except(["index", "show"]);
    Route::apiResource("comment", CommentController::class)->except(["index", "show"]);

    // * Logout route
    Route::post("/auth/logout", [AuthController::class, "logout"]);
});

// * Auth Routes
Route::post("/auth/register", [AuthController::class, "register"]);
Route::post("/auth/login", [AuthController::class, "login"]);
Route::post("/auth/checkCredentials", [AuthController::class, "checkCredentias"]);


// * Public routes
Route::apiResource("post", PostController::class)->only(["index", "show"]);
Route::apiResource("comment", CommentController::class)->only(["index", "show"]);
Route::get("/search", [SearchController::class, "search"]);