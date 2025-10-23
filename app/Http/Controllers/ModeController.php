<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ModeController extends Controller
{
    /**
     * 🌈 モード選択画面を表示
     */
    public function index()
    {
        return view('mode.select');
    }

    /**
     * 💾 モード選択の保存とリダイレクト
     */
    public function store(Request $request)
    {
        // 🔍 バリデーション
        $validated = $request->validate([
            'user_type' => 'required|in:home,company',
        ]);

        // 🧠 入力値を統一形式に変換（home → household）
        $mode = $validated['user_type'] === 'home' ? 'household' : 'company';

        // 💾 セッションに保存
        Session::put('mode', $mode);

        // ✅ グループ選択画面へリダイレクト
        return redirect()
            ->route('group.select')
            ->with('success', ($mode === 'household' ? '家庭用' : '企業用') . 'モードを選択しました。グループを選んでください。');
    }
}
