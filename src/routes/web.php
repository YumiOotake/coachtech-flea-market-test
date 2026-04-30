<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\OrderController;
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

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

Route::middleware('auth')->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    Route::post('/like/{item_id}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item_id}', [LikeController::class, 'destroy'])->name('like.destroy');

    Route::post('comment/{item_id}', [CommentController::class, 'store'])->name('comment.store');


    Route::get('/purchase/{item_id}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/purchase/{item_id}', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/purchase/{item_id}/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/purchase/{item_id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/purchase/address/{item_id}', [OrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/purchase/address/{item_id}', [OrderController::class, 'update'])->name('orders.update');

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [MypageController::class, 'edit'])->name('mypage.edit');
    Route::patch('/mypage/profile', [MypageController::class, 'update'])->name('mypage.update');
});
