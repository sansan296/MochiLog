<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Profile;

class ModeController extends Controller
{
    /**
     * 🌈 モード選択画面を表示
     */
    public function index()
    {
        $user = Auth::user();

        // 現在のモード（セッション or プロフィール or household）
        $currentMode = Session::get('mode');

        if (!$currentMode) {
            $currentMode = optional($user->profile)->user_type ?? 'household';
        }

        return view('mode.select', compact('currentMode'));
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

        // 🧠 内部的に household / enterprise に変換
        $mode = $validated['user_type'] === 'home' ? 'household' : 'enterprise';

        // 💾 セッションにモード保存
        Session::put('mode', $mode);

        // 🔄 選択済みグループ情報をリセット（モード切替時の誤動作防止）
        Session::forget('selected_group_id');

        // 👤 プロフィールにも保存（初回設定 or 更新）
        $user = Auth::user();
        if ($user) {
            $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
            $profile->update(['user_type' => $mode]);
        }

        // ✅ 次ステップ：グループ選択へ
        return redirect()
            ->route('group.select')
            ->with('success', ($mode === 'household' ? '家庭用' : '企業用') . 'モードを選択しました。グループを選択してください。');
    }
}
