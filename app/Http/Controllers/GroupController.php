<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * 🌈 グループ一覧を表示
     * - ログインユーザーが作成したグループを一覧表示
     */
    public function index()
    {
        $groups = Group::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('groups.index', compact('groups'));
    }

    /**
     * ➕ グループ作成フォームを表示
     */
    public function create()
    {
        // 🧭 現在のモード（家庭用 / 企業用）をセッションから取得
        $selectedMode = Session::get('mode', 'household');

        return view('groups.create', compact('selectedMode'));
    }

    /**
     * 💾 グループを保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mode' => 'required|in:household,company',
        ]);

        // 💾 グループ作成
        $group = Group::create([
            'user_id' => Auth::id(),
            'name'    => $validated['name'],
            'mode'    => $validated['mode'],
        ]);

        // 🧠 セッションに選択されたグループを保存（すぐ利用可能に）
        Session::put('selected_group_id', $group->id);

        // ⚡ Ajaxリクエストなら JSON を返す
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'group'   => $group,
            ]);
        }

        // 📦 通常リダイレクト
        return redirect()
            ->route('groups.index')
            ->with('success', 'グループを作成しました。');
    }

    /**
     * ✏️ グループ編集フォームを表示
     */
    public function edit(Group $group)
    {
        $this->authorizeGroup($group);

        return view('groups.edit', compact('group'));
    }

    /**
     * 🔄 グループ更新処理
     */
    public function update(Request $request, Group $group)
    {
        $this->authorizeGroup($group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mode' => 'required|in:household,company',
        ]);

        $group->update($validated);

        return redirect()
            ->route('groups.index')
            ->with('success', 'グループ情報を更新しました。');
    }

    /**
     * 🗑️ グループ削除処理
     */
    public function destroy(Group $group)
    {
        $this->authorizeGroup($group);

        $group->delete();

        // 削除したグループが選択中ならセッションも消去
        if (Session::get('selected_group_id') === $group->id) {
            Session::forget('selected_group_id');
        }

        return redirect()
            ->route('groups.index')
            ->with('success', 'グループを削除しました。');
    }

    /**
     * 🛡️ 権限確認（他人のグループ操作防止）
     */
    private function authorizeGroup(Group $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403, 'このグループを操作する権限がありません。');
        }
    }
}
