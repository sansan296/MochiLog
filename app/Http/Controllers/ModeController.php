<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeController extends Controller
{
    public function select()
    {
        return view('mode.select');
    }

    public function store(Request $request)
    {
        $userType = $request->input('user_type'); 

        if ($userType === 'home') {
            return redirect()->route('dashboard.home');
        } elseif ($userType === 'company') {
            return redirect()->route('dashboard.company');
        }

        return redirect()->back()->with('error', '無効な選択です');
    }
}
