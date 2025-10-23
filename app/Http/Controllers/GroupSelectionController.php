<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Group;

class GroupSelectionController extends Controller
{
    /**
     * 🌈 グループ選択画面を表示
     */
    public function select()
    {
        // ✅ 現在のモード（household / company）をセッションから取得
        $mode = Session::get('mode');

        // 🚨 モードが選択されていない場合は、モード選択画面へリダイレクト
        if (!$mode) {
            return redirect()->route('mode.select')->with('error', 'モードを選択してください。');
        }

        // ✅ ログイン中のユーザー & 現在のモードに一致するグループのみ取得
        $groups = Group::where('user_id', Auth::id())
            ->where('mode', $mode)
            ->orderByDesc('created_at')
            ->get();

        // 🚨 一致するグループが存在しない場合は、新規作成画面へ
        if ($groups->isEmpty()) {
            $message = $mode === 'household'
                ? '家庭用グループを作成してください。'
                : '企業用グループを作成してください。';

            return redirect()->route('groups.create')->with('info', $message);
        }

        return view('groups.select', compact('groups', 'mode'));
    }

    /**
     * 💾 選択されたグループをセッションに保存
     */
    public function set(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        // ✅ 選択したグループをセッションに保存
        Session::put('selected_group_id', $validated['group_id']);

        // ✅ グループ情報を取得
        $group = Group::find($validated['group_id']);

        // ✅ メニュー画面に遷移（在庫画面ではなく）
        return redirect()
            ->route('menu.index')
            ->with('success', "{$group->name}（" . ($group->mode === 'household' ? '家庭用' : '企業用') . "）を選択しました。");
    }
}
