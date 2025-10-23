<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Profile;
use App\Models\Group;

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

        // 🔄 モード切替時は選択グループをリセット
        Session::forget('selected_group_id');

        // 👤 プロフィールにモードを保存
        $user = Auth::user();
        if ($user) {
            $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
            $profile->update(['user_type' => $mode]);
        }

        // ================================================
        // 🧩 グループの存在チェック
        // ================================================
        if ($mode === 'enterprise') {
            // 👥 所属グループを取得
            $group = $user->groups()->first(); // belongsToMany(Group::class) 前提

            if ($group) {
                // ✅ グループが存在 → 企業ダッシュボードへ
                Session::put('selected_group_id', $group->id);
                return redirect()->route('company.dashboard')
                    ->with('success', '企業モードを選択しました。グループ「' . $group->name . '」に参加中です。');
            } else {
                // 🚪 グループ未所属 → 作成ページへ
                return redirect()->route('groups.create')
                    ->with('info', '企業グループが見つかりません。新しいグループを作成してください。');
            }
        } else {
            // 👨‍👩‍👧 家庭モード → 家庭ダッシュボードへ
            return redirect()->route('home.dashboard')
                ->with('success', '家庭用モードを選択しました。');
        }
    }
}
