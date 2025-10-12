<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Memo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function show($mode)
{
    if (!in_array($mode, ['home', 'company'])) {
        abort(404);
    }

    $today = Carbon::today();
    $oneWeekLater = Carbon::today()->addWeek();

    $expiredItems = Item::whereDate('expiration_date', '<', $today)->get();
    $nearExpiredItems = Item::whereDate('expiration_date', '>=', $today)
                            ->whereDate('expiration_date', '<=', $oneWeekLater)
                            ->get();
    $memos = Memo::with(['item', 'user'])->latest()->get();

    return view("dashboard.$mode", compact('expiredItems', 'nearExpiredItems', 'memos'));
}


}
