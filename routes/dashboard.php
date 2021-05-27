<?php
declare(strict_types=1);

use App\Http\Controllers\Backend\AuthorizeController;
use App\Http\Controllers\Backend\DecorationController;
use App\Http\Controllers\Backend\ExtensionController;
use App\Http\Controllers\Backend\ManagerController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SiteController;
use App\Http\Controllers\Backend\TaxonomyController;
use App\Http\Controllers\Backend\CKFinderController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\OpenController;
use App\Http\Controllers\Backend\GlobalController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
|
*/

Route::post('/authorize/register', [AuthorizeController::class, 'register']);
Route::post('/authorize/login', [AuthorizeController::class, 'authenticate']);
Route::post('/authorize/logout', [AuthorizeController::class, 'logout']);
Route::get('/open/configuration', [OpenController::class, 'configuration']);

Route::middleware(['auth:backend', 'rbac'])->group(callback: function() {
    Route::get('/dashboard', [DashboardController::class, 'main']);
    Route::any('/ckfinder/connector', [CKFinderController::class, 'request']);
    Route::get('/open/user/menu', [OpenController::class, 'userMenu']);
    Route::get('/open/menus', [OpenController::class, 'menus']);
    Route::post('/open/menu/refresh', [OpenController::class, 'menuRefresh']);

    Route::get('/user/members', [UserController::class, 'members']);

    Route::get('/settings', [GlobalController::class, 'options']);
    Route::post('/setting/store', [GlobalController::class, 'store']);
    Route::post('/setting/update/{option}', [GlobalController::class, 'update']);

    Route::get('/site/options', [SiteController::class, 'options']);
    Route::post('/site/option/save', [SiteController::class, 'saveGeneral']);

    Route::get('/site/ad/options', [SiteController::class, 'adOptions']);
    Route::post('/site/ad/option/save', [SiteController::class, 'saveAd']);


    Route::get('/posts', [PostController::class, 'posts']);
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::post('/post/store', [PostController::class, 'store']);
    Route::post('/post/update/{id}', [PostController::class, 'update']);
    Route::post('/post/delete/{id}', [PostController::class, 'delete']);

    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/page/{id}', [PageController::class, 'show']);
    Route::post('/page/store', [PageController::class, 'store']);
    Route::post('/page/update/{id}', [PageController::class, 'update']);
    Route::post('/page/delete/{id}', [PageController::class, 'delete']);


    Route::get('/tags', [TaxonomyController::class, 'tags']);
    Route::post('/tag/store', [TaxonomyController::class, 'storeTag']);
    Route::post('/tag/update/{id}', [TaxonomyController::class, 'updateTag']);
    Route::delete('/tag/delete/{id}', [TaxonomyController::class, 'deleteTag']);

    Route::get('/categories', [TaxonomyController::class, 'categories']);
    Route::post('/category/store', [TaxonomyController::class, 'storeCategory']);
    Route::post('/category/update/{id}', [TaxonomyController::class, 'updateCategory']);
    Route::delete('/category/delete/{id}', [TaxonomyController::class, 'deleteCategory']);

    Route::get('/managers', [ManagerController::class, 'members']);
    Route::post('/manager/store', [ManagerController::class, 'store']);
    Route::post('/manager/update/{user}', [ManagerController::class, 'update']);
    Route::delete('/manager/delete/{user}', [ManagerController::class, 'delete']);


    Route::get('/roles', [RoleController::class, 'roles']);
    Route::post('/role/store', [RoleController::class, 'store']);
    Route::post('/role/update/{role}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{role}', [RoleController::class, 'delete']);

    Route::get('/navigate/struct/data', [DecorationController::class, 'navigateStructData']);
    Route::get('/navigate/{id}', [DecorationController::class, 'navigate']);
    Route::post('/navigate/save', [DecorationController::class, 'saveNavigate']);
    Route::delete('/navigate/{id}/delete', [DecorationController::class, 'deleteNavigate']);

    Route::get('/themes', [DecorationController::class, 'themes']);


    Route::post('/extension/meta/save', [ExtensionController::class, 'saveMeta']);
    Route::get('/extension/meta/{taxonomy}', [ExtensionController::class, 'taxonomy']);
});



