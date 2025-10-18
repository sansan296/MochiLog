<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'notify_low_stock' => 'nullable|boolean',
            'notify_recipe_updates' => 'nullable|boolean',
            'notify_system' => 'nullable|boolean',
        ]);

        // チェックボックスがオフのときは null が送られるので false に変換
        $user->update([
            'notify_low_stock' => $request->has('notify_low_stock'),
            'notify_recipe_updates' => $request->has('notify_recipe_updates'),
            'notify_system' => $request->has('notify_system'),
        ]);

        return redirect()->route('settings.index')->with('success', '通知設定を保存しました。');
    }
}
