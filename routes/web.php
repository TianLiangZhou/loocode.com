<?php

declare(strict_types=1);

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\InstallController;
use App\Http\Controllers\Frontend\OauthController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ToolController;
use App\Http\Controllers\Frontend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, "index"])->name("index");
Route::get("/tag/{name}", [PostController::class, "taxonomy"]);
Route::get("/category/{name}", [PostController::class, "taxonomy"]);
Route::get("/post/{id}", [PostController::class, "show"])
    ->where("id", "[0-9]+");
Route::post('/post/like/{id}', [PostController::class, "like"])->where('id', '[0-9]+');
Route::post('/post/comment/{id}', [PostController::class, "comment"])->where('id', '[0-9]+');
Route::get("/oauth/{endpoint}", [OauthController::class, "endpoint"])
    ->where("endpoint", "qq|github");
Route::get("/oauth/callback/{endpoint}", [OauthController::class, "callback"])
    ->where("endpoint", "qq|github");

Route::get('/logout', [OauthController::class, 'logout']);

Route::prefix("user")->middleware(["auth"])->group(function() {
    Route::get("/setting", [UserController::class, 'setting']);
    Route::post("/update", [UserController::class, 'update']);
    Route::post("/upload", [UserController::class, 'upload']);
});
Route::get("/search", [HomeController::class, 'search']);


Route::get("/install", [InstallController::class, 'step']);
Route::post("/install", [InstallController::class, 'activate']);

Route::get("/tools", [ToolController::class, 'index']);
Route::get("/tool/pinyin/chinese-to-pinyin", [ToolController::class, 'tool']);
Route::get("/tool/opencc/simplified-chinese-to-traditional-chinese", [ToolController::class, 'tool']);
Route::get("/tool/lac/chinese-word-segmentation", [ToolController::class, 'tool']);
Route::get("/tool/qrcode/qr-code-generator", [ToolController::class, 'tool']);
Route::get("/tool/ocr/ocr-recognition", [ToolController::class, 'tool']);
