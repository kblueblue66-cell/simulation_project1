<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
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

// 商品一覧画面（トップ画面） [3][1]
Route::get('/', [ItemController::class, 'index'])->name('item.index');

// 商品詳細画面 [1]
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

Route::middleware(['auth'])->group(function () {

    Route::middleware(['verified'])->group(function(){
    // 購入完了時のリダイレクト先を追加 [17: FN022]
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');
    // 商品出品画面 [1]
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

    // 商品購入画面 [1]
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 送付先住所変更画面 [1]
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');

    // プロフィール画面 [1]
    // ※購入一覧・出品一覧はパラメータ(?page=buy/sell)で制御 [1]
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');

    // プロフィール編集画面（設定画面） [3][1]
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    // コメント送信用のルート定義
    Route::post('/item/{item_id}/comment', [ItemController::class, 'storeComment'])
    ->name('comment.store');

    // いいね登録用のルート定義
    Route::post('/item/{item_id}/like', [LikeController::class, 'store'])
    ->name('like');

    });

});