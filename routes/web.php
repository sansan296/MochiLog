<?php

use App\Http\Controllers\{
    AuditLogController,
    ProfileController,
    ModeController,
    MemoController,
    ItemController,
    RecipeController,
    PurchaseListController,
    DashboardController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

// ログイン後、選択ページへリダイレクト
Route::get('/dashboard', fn() => redirect('/mode-select'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 家庭・企業の選択ページ
Route::get('/mode-select', [ModeController::class, 'select'])
    ->middleware('auth')
    ->name('mode.select');
Route::post('/mode-select', [ModeController::class, 'store'])
    ->middleware('auth')
    ->name('mode.store');

// 家庭・企業別ダッシュボード
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/home', [DashboardController::class, 'home'])->name('dashboard.home');
    Route::get('/dashboard/company', [DashboardController::class, 'company'])->name('dashboard.company');

    // レシピ
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

    // 在庫・メモ
    Route::resource('items', ItemController::class);
    Route::resource('items.memos', MemoController::class);

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 購入リスト
    Route::get('/purchase-lists', [PurchaseListController::class, 'index'])->name('purchase_lists.index');
    Route::post('/purchase-lists', [PurchaseListController::class, 'store'])->name('purchase_lists.store');
    Route::delete('/purchase-lists/{purchaseList}', [PurchaseListController::class, 'destroy'])
    ->whereNumber('purchaseList')
    ->name('purchase_lists.destroy');



    // 監査ログ
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // routes/web.php（authグループ内）
    Route::get('/purchase-lists/audit-logs', fn () => redirect()->route('audit-logs.index'))
    ->name('legacy.audit-logs');

});


require __DIR__.'/auth.php';
