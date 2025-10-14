<?php

use App\Http\Controllers\{
    AuditLogController,
    ProfileController,
    ModeController,
    MemoController,
    ItemController,
    RecipeController,
    PurchaseListController,
    DashboardController,
    AdminController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| ここでは、ユーザーと管理者それぞれのルートを定義します。
| 「auth」ミドルウェアで通常ログイン済みユーザーを保護し、
| 「auth:admin」で管理者ログイン専用の領域を保護します。
|--------------------------------------------------------------------------
*/

// ----------------------------------------
// 🌟 トップページ
// ----------------------------------------
Route::get('/', fn() => view('welcome'));

// ----------------------------------------
// 🌟 ログイン後：モード選択へリダイレクト
// ----------------------------------------
Route::get('/dashboard', fn() => redirect('/mode-select'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ----------------------------------------
// 🌟 家庭・企業の選択ページ
// ----------------------------------------
Route::get('/mode-select', [ModeController::class, 'select'])
    ->middleware('auth')
    ->name('mode.select');
Route::post('/mode-select', [ModeController::class, 'store'])
    ->middleware('auth')
    ->name('mode.store');

// ----------------------------------------
// 🌟 一般ユーザー用ルート（auth ミドルウェア）
// ----------------------------------------
Route::middleware('auth')->group(function () {

    // ダッシュボード（家庭 / 企業）
    Route::get('/dashboard/home', [DashboardController::class, 'home'])->name('dashboard.home');
    Route::get('/dashboard/company', [DashboardController::class, 'company'])->name('dashboard.company');

    // レシピ
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

    // 在庫・メモ（リソースルート）
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

    // 旧ルート互換（監査ログ）
    Route::get('/purchase-lists/audit-logs', fn () => redirect()->route('audit-logs.index'))
        ->name('legacy.audit-logs');
});


// ====================================================================
// 🌟 管理者用ルート群
// ====================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // 管理者ログインページ
    Route::get('/login', [AdminController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [AdminController::class, 'login'])
        ->name('login.submit');

    // 管理者専用領域（auth:admin ミドルウェア保護）
    Route::middleware('auth:admin')->group(function () {

        // 管理者ダッシュボード
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        // 管理者ログアウト
        Route::post('/logout', [AdminController::class, 'logout'])
            ->name('logout');
    });
});


// ----------------------------------------
// 🌟 Laravel Breeze / Jetstream 認証ルート
// ----------------------------------------
require __DIR__ . '/auth.php';
