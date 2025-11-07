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
     * 🔄 管理者権限をトグル（自分の権限切替）
     */
    public function toggleSelf(Request $request)
    {
        /** 
         * @var \App\Models\User $user 
         * LaravelのAuth::user()は User|null を返すため、
         * 静的解析ツール（Intelephense）に正しい型を明示。
         * これにより「Undefined method 'save'」警告を抑止。
         */
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'ログインしていません。もう一度ログインしてください。');
        }

        // 管理者権限を切り替え（admin ⇄ 一般）
        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? '管理者' : '一般ユーザー';
        return back()->with('success', "権限を「{$status}」に切り替えました。");
    }
}
