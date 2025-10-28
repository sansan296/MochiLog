<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupInvitationController extends Controller
{
    /**
     * LINE招待リンク受け取り処理
     */
    public function accept($token)
    {
        // ✅ 招待情報を確認
        $invitation = GroupInvitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('menu.index')->with('error', '無効な招待リンクです。');
        }

        $group = Group::find($invitation->group_id);

        if (!$group) {
            return redirect()->route('menu.index')->with('error', '対象のグループが存在しません。');
        }

        // ✅ 未ログインならログインページへ
        if (!Auth::check()) {
            session(['pending_invite_token' => $token]); // 後で続き処理用
            return redirect()->route('login')->with('info', 'ログイン後にグループへ参加します。');
        }

        $user = Auth::user();

        // ✅ すでにメンバーならスキップ
        if ($group->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('menu.index')->with('info', 'すでにこのグループに参加しています。');
        }

        // ✅ グループに追加
        $group->members()->attach($user->id, ['role' => 'member']);
        $invitation->update(['accepted' => true]);

        return redirect()->route('menu.index')->with('success', "グループ「{$group->name}」に参加しました！");
    }
}
