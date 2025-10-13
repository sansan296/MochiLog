<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModeController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\PurchaseListController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ログイン後、選択ページへリダイレクト
Route::get('/dashboard', function () {
    return redirect('/select');
})->middleware(['auth', 'verified'])->name('dashboard');

// 家庭・企業の選択ページ
Route::get('/select', function () {
    return view('mode.select');
})->middleware('auth')->name('select');

// 家庭・企業の選択ページ（GET）
Route::get('/mode-select', [ModeController::class, 'select'])
    ->middleware('auth')
    ->name('mode.select');

// 家庭・企業の選択内容を処理（POST）
Route::post('/mode-select', [ModeController::class, 'store'])
    ->middleware('auth')
    ->name('mode.store');

// 家庭用ダッシュボード
Route::get('/dashboard/home', [DashboardController::class, 'home'])
    ->name('dashboard.home')
    ->middleware('auth');

// 企業用ダッシュボード
Route::get('/dashboard/company', [DashboardController::class, 'company'])
    ->name('dashboard.company')
    ->middleware('auth');

// レシピページ
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

// ログイン後にのみ利用可能なルート
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


    Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


require __DIR__.'/auth.php';
