<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminPassword;
use Illuminate\Support\Facades\Hash;

class AdminGateController extends Controller
{
    /**
     * 🔐 管理者パスワード入力フォームを表示
     */
    public function show()
    {
        $defaultPassword = env('DEFAULT_ADMIN_PASSWORD', '0000');

        // ✅ モデルが自動でハッシュ化するため、ここでは Hash::make() は不要
        if (!AdminPassword::exists()) {
            AdminPassword::create(['password' => $defaultPassword]);
            session()->flash('first_time_info', "初回パスワード：{$defaultPassword}");
        }

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

        $stored = AdminPassword::first();

        if ($stored && Hash::check($request->password, $stored->password)) {
            // 💡 ミドルウェアと合わせる
            session(['admin_authenticated' => true]);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', '管理者認証に成功しました。');
        }

        return back()
            ->withErrors(['password' => 'パスワードが正しくありません。'])
            ->withInput();
    }
}
