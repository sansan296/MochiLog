<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ModeController extends Controller
{
    public function select(Request $request)
    {
        $type = $request->input('user_type');

        Session::put('user_type', $type);

        if ($type === 'home') {
            return redirect()->route('dashboard.home');
        } else {
            return redirect()->route('dashboard.company');
        }
    }
}
