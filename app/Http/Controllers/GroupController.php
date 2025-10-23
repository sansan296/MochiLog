<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * 🌈 グループ一覧を表示
     * - ログインユーザーが作成したグループを一覧表示
     */
    public function index()
    {
        $groups = Group::where('user_id', Auth::id())->orderByDesc('created_at')->get();

        return view('groups.index', compact('groups'));
    }

    /**
     * ➕ グループ作成フォームを表示
     */
    public function create()
    {
        // モード選択済みであれば事前に表示用に渡す
        $selectedMode = session('mode');
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

        // グループ作成
        $group = Group::create([
            'user_id' => Auth::id(),
            'name'    => $validated['name'],
            'mode'    => $validated['mode'],
        ]);

        // AjaxリクエストならJSONレスポンス
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'group'   => $group,
            ]);
        }

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
        ]);

        $group->update($validated);

        return redirect()
            ->route('groups.index')
            ->with('success', 'グループ名を更新しました。');
    }

    /**
     * 🗑️ グループ削除処理
     */
    public function destroy(Group $group)
    {
        $this->authorizeGroup($group);

        $group->delete();

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
