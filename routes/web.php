<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuditLogController,
    ProfileController,
    ModeController,
    MemoController,
    ItemController,
    IngredientController,
    RecipeController,
    RecipeBookmarkController,
    PurchaseListController,
    DashboardController,
    AdminController,
    TagController,
    ItemTagController,
    InventoryCsvController,
    SettingsController,
    CalendarEventController,
    AdminGateController,
    GroupController,
    GroupSelectionController,
    GroupMemberController,
    GroupInvitationController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| 一般ユーザー・管理者・グループ・招待機能を含むルート定義
|--------------------------------------------------------------------------
*/

// ====================================================================
// 🌟 トップページ
// ====================================================================
Route::get('/', fn() => view('welcome'));

// 🌟 ログイン後：モード選択ページへリダイレクト
Route::get('/dashboard', fn() => redirect()->route('mode.select'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ====================================================================
// 🌟 家庭・企業のモード選択
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/mode-select', [ModeController::class, 'index'])->name('mode.select');
    Route::post('/mode-select', [ModeController::class, 'store'])->name('mode.store');
});

// ====================================================================
// 🌟 グループ関連（家庭用・企業用を分けたチーム管理）
// ====================================================================
Route::middleware(['auth'])->group(function () {
    // グループ基本操作
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

   // グループ選択（モード選択後に表示）
    Route::get('/group/select', [GroupSelectionController::class, 'select'])->name('group.select');
    Route::post('/group/set', [GroupSelectionController::class, 'set'])->name('group.set');

    // メンバー管理
    Route::get('/groups/{group}/members', [GroupMemberController::class, 'index'])->name('group.members.index');
    Route::get('/groups/{group}/members/create', [GroupMemberController::class, 'create'])->name('group.members.create');
    Route::post('/groups/{group}/members', [GroupMemberController::class, 'store'])->name('group.members.store');
    Route::delete('/groups/{group}/members/{user}', [GroupMemberController::class, 'destroy'])->name('group.members.destroy');

    // 招待リンク
    Route::get('/group/invite/{token}', [GroupInvitationController::class, 'accept'])->name('group.invite.accept');
});

// ====================================================================
// 🌟 一般ユーザー用ルート群
// ====================================================================
Route::middleware(['auth'])->group(function () {

    // 🧭 メニュー
    Route::get('/menu', fn() => view('menu.index'))->name('menu.index');

    // ⚙️ 設定
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // 🏠 ダッシュボード
    Route::get('/dashboard/home', [DashboardController::class, 'home'])->name('dashboard.home');
    Route::get('/dashboard/company', [DashboardController::class, 'company'])->name('dashboard.company');

    // 🍳 レシピ
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])->whereNumber('id')->name('recipes.show');

    // 🔖 ブックマーク
    Route::get('/bookmarks', [RecipeBookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [RecipeBookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{id}', [RecipeBookmarkController::class, 'destroy'])->whereNumber('id')->name('bookmarks.destroy');

    // 🗓️ カレンダー
    Route::get('/calendar', [CalendarEventController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarEventController::class, 'fetch'])->name('calendar.fetch');
    Route::post('/calendar/events', [CalendarEventController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/events/{event}', [CalendarEventController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/events/{event}', [CalendarEventController::class, 'destroy'])->name('calendar.destroy');
    Route::post('/calendar/events/{event}/complete', [CalendarEventController::class, 'complete'])->name('calendar.complete');
    Route::get('/calendar/history', [CalendarEventController::class, 'history'])->name('calendar.history');
    Route::get('/calendar/date', [CalendarEventController::class, 'getByDate'])->name('calendar.byDate');

    // 📦 在庫（Item）
    Route::resource('items', ItemController::class);
    Route::post('/items/{item}/pin', [ItemController::class, 'togglePin'])->name('items.pin');
    Route::resource('items.memos', MemoController::class);

    // 🥦 食材
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    // 🏷 タグ
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    // 🏷 アイテムごとのタグ操作
    Route::get('/items/{item}/tags', [ItemTagController::class, 'index'])->name('items.tags.index');
    Route::post('/items/{item}/tags/toggle', [ItemTagController::class, 'toggle'])->name('items.tags.toggle');

    // 🛒 購入リスト
    Route::get('/purchase-lists', [PurchaseListController::class, 'index'])->name('purchase_lists.index');
    Route::post('/purchase-lists', [PurchaseListController::class, 'store'])->name('purchase_lists.store');
    Route::delete('/purchase-lists/{purchaseList}', [PurchaseListController::class, 'destroy'])->whereNumber('purchaseList')->name('purchase_lists.destroy');

    // 👤 プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/view', [ProfileController::class, 'show'])->name('profile.view');
});

// ====================================================================
// 📊 CSVインポート・エクスポート（管理者専用）
// ====================================================================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/items/csv', [InventoryCsvController::class, 'index'])->name('items.csv.index');
    Route::post('/items/csv/export', [InventoryCsvController::class, 'export'])->name('items.csv.export');
    Route::post('/items/csv/import', [InventoryCsvController::class, 'import'])->name('items.csv.import');
    Route::get('/items/csv/template', [InventoryCsvController::class, 'template'])->name('items.csv.template');
});

// ====================================================================
// 💡 管理者パスワードゲート
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/password-gate', [AdminGateController::class, 'show'])->name('admin.password.gate.show');
    Route::post('/admin/password-gate', [AdminGateController::class, 'check'])->name('admin.password.gate.check');
});

// ====================================================================
// 🌟 管理者専用ルート（共通パスワード認証）
// ====================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::put('/update-admin-password', [SettingsController::class, 'updateAdminPassword'])->name('password.update');
});

Route::post('/settings/update-admin-password', [SettingsController::class, 'updateAdminPassword'])
    ->middleware(['auth', 'admin.access'])
    ->name('settings.updateAdminPassword');

// ====================================================================
// 📜 監査ログ（管理者専用）
// ====================================================================
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

// ====================================================================
// 🌟 旧ルート（互換性用、今後削除可）
// ====================================================================
Route::middleware(['auth'])->get('/admin/settings-dashboard', fn() => view('admin.dashboard'))
    ->name('admin.settings.dashboard');

// ====================================================================
// 🌟 Laravel Breeze / Jetstream 認証ルート
// ====================================================================
require __DIR__ . '/auth.php';
