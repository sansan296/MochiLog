<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class GroupSelectionController extends Controller
{
    /**
     * 🌈 モード選択後に表示するグループ選択画面
     */
    public function select()
    {
        $user = Auth::user();
        $mode = Session::get('mode');

        // モードが未選択の場合はモード選択ページに戻す
        if (!$mode) {
            return redirect()->route('mode.select')->with('error', 'モードを選択してください。');
        }

        // ログインユーザーのそのモードに属するグループを取得
        $groups = Group::where('user_id', $user->id)
            ->where('mode', $mode)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.select', compact('groups', 'mode'));
    }

    /**
     * 💾 選択されたグループをセッションに保存
     */
    public function choose(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $group = Group::findOrFail($validated['group_id']);

        // 🧩 ログインユーザーがそのグループの作成者であることを確認
        if ($group->user_id !== Auth::id()) {
            abort(403, 'このグループを選択する権限がありません。');
        }

        // 💾 セッションに保存
        Session::put('group_id', $group->id);
        Session::put('group_name', $group->name);

        // ✅ 在庫一覧（items.index）へリダイレクト
        return redirect()
            ->route('items.index')
            ->with('success', 'グループ「' . $group->name . '」を選択しました。');
    }
}
