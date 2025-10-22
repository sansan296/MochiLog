<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminPassword; // 追加
use Illuminate\Support\Facades\Hash;

class AdminGateController extends Controller
{
    /**
     * 🔐 管理者パスワード入力フォームを表示
     */
    public function show()
    {
        return view('admin.gate');
    }

    /**
     * ✅ パスワード認証処理
     */
    public function check(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // 💡 初回アクセス時、自動で初期パスワード登録
        if (!AdminPassword::exists()) {
            AdminPassword::create(['password' => 'admin1234']);
            // 初回だけセッションにメッセージを保存
            session()->flash('first_time_info', '初回パスワード admin1234 が自動登録されました。');
        }

        // 登録済みパスワードを取得
        $stored = AdminPassword::first();

        // 入力と照合
        if ($stored && Hash::check($request->password, $stored->password)) {
            session(['admin_verified' => true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'パスワードが正しくありません。'])->withInput();
    }
}
