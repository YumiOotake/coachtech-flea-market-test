<?php

use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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

Route::post('/webhook/stripe', [WebhookController::class, 'handle']);


Route::middleware(['auth', 'verified'])->group(function () {
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

//メールのURLクリックで表示//未認証の人がアクセスすると表示
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//認証済みにするemail_verified_at に日時入れる
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('mypage.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

//認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
