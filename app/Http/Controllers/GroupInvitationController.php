<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupInvitationController extends Controller
{
    /**
     * 📩 招待リンクを受け取ったときの処理
     */
    public function accept($token)
    {
        // ✅ 招待トークンを確認
        $invitation = GroupInvitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('menu.index')->with('error', '無効な招待リンクです。');
        }

        // ✅ 既に承認済み（再利用防止）
        if ($invitation->accepted) {
            return redirect()->route('menu.index')->with('info', 'この招待リンクはすでに使用されています。');
        }

        $group = Group::find($invitation->group_id);

        if (!$group) {
            return redirect()->route('menu.index')->with('error', '対象のグループが存在しません。');
        }

        // ✅ 未ログインなら、トークンをセッションに保存してログインページへ
        if (!Auth::check()) {
            session(['pending_invite_token' => $token]);
            return redirect()->route('login')->with('info', 'ログイン後にグループへ参加します。');
        }

        // ✅ ログイン済みならそのまま処理
        $user = Auth::user();

        // すでに参加済みか確認
        if ($group->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('menu.index')->with('info', 'すでにこのグループに参加しています。');
        }

        // ✅ グループに追加
        $group->members()->attach($user->id, ['role' => 'member']);

        // 招待を承認済みに更新
        $invitation->update(['accepted' => true]);

        return redirect()->route('menu.index')->with('success', "グループ「{$group->name}」に参加しました！");
    }

    /**
     * 🔁 ログイン後に自動で招待処理を継続する
     */
    public function handlePendingInvite()
    {
        if (session()->has('pending_invite_token')) {
            $token = session('pending_invite_token');
            session()->forget('pending_invite_token');
            return redirect()->route('group.invite.accept', ['token' => $token]);
        }

        return redirect()->route('menu.index');
    }
}
