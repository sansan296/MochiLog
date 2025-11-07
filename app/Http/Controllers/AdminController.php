<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GroupMember;

class AdminController extends Controller
{
    public function __construct()
    {
        // 🔒 全メソッドをログインユーザー限定に
        $this->middleware('auth');
    }

    /**
     * 🧩 管理者ダッシュボード
     */
    public function dashboard()
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ 現在のグループ所属ユーザーを取得
        $users = User::whereIn('id', function ($query) use ($groupId) {
            $query->select('user_id')
                ->from('group_members')
                ->where('group_id', $groupId);
        })->orderBy('id')->get();

        $authUser = Auth::user();

        return view('admin.dashboard', compact('users', 'authUser'));
    }

    /**
     * 🔄 自分の権限を切り替える
     */
    public function toggleSelf(Request $request)
    {
        /** 
         * @var \App\Models\User $user 
         */
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'ログインしていません。もう一度ログインしてください。');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? '管理者' : '一般ユーザー';
        return back()->with('success', "あなたの権限を「{$status}」に切り替えました。");
    }

    /**
     * 🧑‍🤝‍🧑 他人の権限を切り替える（誰でも実行可能）
     */
    public function toggleUser(Request $request, User $user)
    {
        $groupId = session('selected_group_id');

        if (!$groupId) {
            return back()->with('error', 'グループが選択されていません。');
        }

        // ✅ 同じグループのユーザーのみ切り替え可能
        $isMember = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return back()->with('error', 'このユーザーは選択中のグループに所属していません。');
        }

        // ✅ 権限をトグル（admin ⇄ 一般）
        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? '管理者' : '一般ユーザー';
        return back()->with('success', "{$user->name} さんを「{$status}」に設定しました。");
    }
}
