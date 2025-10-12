<?php

use App\Http\Controllers\MemoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\PurchaseListController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $today = \Carbon\Carbon::today();

    $expiredItems = \App\Models\Item::where('user_id', auth()->id())
        ->whereDate('expiration_date', '<', $today)
        ->get();

    $nearExpiredItems = \App\Models\Item::where('user_id', auth()->id())
        ->whereDate('expiration_date', '>=', $today)
        ->whereDate('expiration_date', '<=', $today->copy()->addWeek())
        ->get();

    //ユーザーのメモ取得
    $memos = \App\Models\Memo::with('item', 'user')
        ->whereHas('item', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->latest()
        ->get();

    return view('dashboard', compact('expiredItems', 'nearExpiredItems', 'memos'));
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

Route::middleware(['auth'])->group(function () {
    // 在庫
    Route::resource('items', ItemController::class);
    Route::resource('items.memos', MemoController::class);

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 購入リスト
    Route::get('/purchase-lists', [PurchaseListController::class, 'index'])->name('purchase_lists.index');
    Route::post('/purchase-lists', [PurchaseListController::class, 'store'])->name('purchase_lists.store');
    Route::delete('/purchase-lists/{purchaseList}', [PurchaseListController::class, 'destroy'])->name('purchase_lists.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');


require __DIR__.'/auth.php';
