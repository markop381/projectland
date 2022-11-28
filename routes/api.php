<?php

use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Buyer\BuyerArticleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Seller\SellerArticleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//LOGIN
Route::post('login', [LoginController::class, 'login']);
Route::post('register', [LoginController::class, 'register']);

Route::middleware(['jwtMiddle'])->group(function () {

    Route::middleware(['isAdmin'])->prefix('admin')->group(function () {
        //ADMIN ARTICLE
        Route::get('articles', [AdminArticleController::class, 'index']);
        Route::get('articles/{article}', [AdminArticleController::class, 'show']);
        Route::post('articles', [AdminArticleController::class, 'store']);
        Route::put('articles/{article}', [AdminArticleController::class, 'update']);
        Route::delete('articles/{article}', [AdminArticleController::class, 'destroy']);
    });

    Route::middleware(['isSeller'])->prefix('seller')->group(function () {
        //SELLER ARTICLE
        Route::put('articles/{article}', [SellerArticleController::class, 'update']);
    });

    Route::middleware(['isBuyer'])->prefix('buyer')->group(function () {
        //BUYER ARTICLE
        Route::get('articles', [BuyerArticleController::class, 'index']);
        Route::post('articles/{article}', [BuyerArticleController::class, 'buy']);
    });

});
