<?php

namespace App\Http\Controllers;

use App\Models\AdminPassword; // 💡 管理者パスワードモデル
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * 設定画面を表示
     */
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * ユーザー設定を更新
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 🔸 デバッグログ
        \Log::info('Settings update request:', $request->all());

        // 入力検証
        $validated = $request->validate([
            'low_stock_threshold' => 'required|integer|min:1|max:50',
        ]);

        // チェックボックス（未チェック時は送信されない）
        $user->notify_low_stock = $request->boolean('notify_low_stock');
        $user->notify_recipe_updates = $request->boolean('notify_recipe_updates');
        $user->notify_system = $request->boolean('notify_system');
        $user->low_stock_threshold = $validated['low_stock_threshold'];
        $user->save();

        // 最新状態で再表示
        $user->refresh();

        return redirect()->route('settings.index')->with('success', '設定を保存しました。');
    }

    /**
     * 共通管理者パスワードを更新する
     * （admin.access ミドルウェアで保護される想定）
     */
    public function updateAdminPassword(Request $request)
    {
        // 必須、文字列、8文字以上、確認フィールドとの一致を検証
        $request->validate([
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 💡 AdminPasswordレコードを取得または新規作成
        $adminPassword = AdminPassword::firstOrNew();

        // AdminPasswordモデルのミューテータで自動ハッシュ化
        $adminPassword->password = $request->admin_password;
        $adminPassword->save();

        // 成功メッセージ付きでリダイレクト
        return redirect()->back()->with('success', '共通管理者パスワードが正常に更新されました。');
    }
}
