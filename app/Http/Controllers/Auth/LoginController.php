<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected function authenticated(Request $request, $user)
    {
        // 招待リンクから来ていた場合、続行
        if (session()->has('pending_invite_token')) {
            return redirect()->route('group.invite.pending');
        }

        return redirect()->intended('/menu'); // 通常の遷移
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
