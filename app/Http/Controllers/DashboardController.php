<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Memo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $oneWeekLater = Carbon::today()->addWeek();

        $expiredItems = Item::whereDate('expiration_date', '<', $today)->get();

        $nearExpiredItems = Item::whereDate('expiration_date', '>=', $today)
                                ->whereDate('expiration_date', '<=', $oneWeekLater)
                                ->get();

        $memos = Memo::with(['item', 'user'])->latest()->get();

        return view('dashboard', compact('expiredItems', 'nearExpiredItems', 'memos'));
    }
}
