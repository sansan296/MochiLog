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
        // 🌟 現在のモードをセッションから取得
        $selectedMode = session('mode');

        if (!$selectedMode) {
            return redirect()
                ->route('mode.select')
                ->with('error', 'モードを選択してください。');
        }

        // ⚙️ モードをビューに渡してフォームで表示のみ（編集不可）
        return view('groups.create', compact('selectedMode'));
    }

    /**
     * 💾 グループを保存
     */
    public function store(Request $request)
    {
        // 🌟 現在のモードをセッションから固定取得
        $currentMode = session('mode');

        if (!$currentMode) {
            return redirect()
                ->route('mode.select')
                ->with('error', 'モードを選択してください。');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // ✅ モードをリクエストからではなく、セッション値で固定
        $group = Group::create([
            'user_id' => Auth::id(),
            'name'    => $validated['name'],
            'mode'    => $currentMode, // ← セッションモードを強制使用
        ]);

        // AjaxリクエストならJSONレスポンス
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'group'   => $group,
            ]);
        }

        return redirect()
            ->route('groups.select')
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
