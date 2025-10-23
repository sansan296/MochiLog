<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Group;

class GroupSelectionController extends Controller
{
    /**
     * グループ選択画面を表示
     */
    public function select()
    {
        $groups = Group::where('user_id', Auth::id())->get();

        if ($groups->isEmpty()) {
            return redirect()->route('groups.create')->with('info', 'まずはグループを作成してください。');
        }

        // 🌈 モードをセッションから取得（例: household / company）
        $mode = Session::get('mode', 'household'); // デフォルトは家庭用

        return view('groups.select', compact('groups', 'mode'));
    }

    /**
     * 選択されたグループをセッションに保存
     */
    public function set(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        // グループ選択をセッションに保存
        session(['selected_group_id' => $validated['group_id']]);

        // グループを取得
        $group = Group::find($validated['group_id']);

        return redirect()
            ->route('items.index')
            ->with('success', "{$group->name}（" . ($group->mode === 'household' ? '家庭用' : '企業用') . "）を選択しました。");
    }
}
