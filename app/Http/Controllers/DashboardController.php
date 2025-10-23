<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Memo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * 🏠 家庭モード用ダッシュボード（グループ対応）
     */
    public function home()
    {
        $user = Auth::user();
        $groupId = session('selected_group_id');

        // グループ未選択なら選択画面へ
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        $threshold = $user->low_stock_threshold ?? 3;
        $today = Carbon::today();
        $oneWeekLater = Carbon::today()->addWeek();

        // ----------------------------------------------------
        // 📦 グループ内データ取得
        // ----------------------------------------------------
        $expiredItems = Item::where('group_id', $groupId)
            ->whereDate('expiration_date', '<', $today)
            ->get();

        $nearExpiredItems = Item::where('group_id', $groupId)
            ->whereDate('expiration_date', '>=', $today)
            ->whereDate('expiration_date', '<=', $oneWeekLater)
            ->get();

        $memos = Memo::with(['item', 'user'])
            ->where('group_id', $groupId)
            ->latest()
            ->take(10)
            ->get();

        // ----------------------------------------------------
        // 🔔 通知生成（ユーザー設定に応じて）
        // ----------------------------------------------------
        $notifications = [];

        // 🧂 在庫が少ない商品通知
        if ($user->notify_low_stock ?? false) {
            $lowStockItems = Item::where('group_id', $groupId)
                ->where('quantity', '<', $threshold)
                ->get();

            if ($lowStockItems->count() > 0) {
                $names = $lowStockItems->pluck('item')->take(3)->implode('、');
                $notifications[] = "在庫が{$threshold}個未満になっています：{$names}";
            }
        }

        // ⚙️ システム通知
        if ($user->notify_system ?? false) {
            $notifications[] = "システムメンテナンスは 10月22日 03:00〜04:00 に予定されています。";
        }

        // ----------------------------------------------------
        // 🖥️ ビューへデータ送信
        // ----------------------------------------------------
        return view('dashboard.home', compact(
            'expiredItems',
            'nearExpiredItems',
            'memos',
            'notifications',
            'threshold',
            'groupId'
        ));
    }

    /**
     * 🏢 企業モード用ダッシュボード（グループ対応）
     */
    public function company()
    {
        $user = Auth::user();
        $groupId = session('selected_group_id');

        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        $threshold = $user->low_stock_threshold ?? 3;
        $today = Carbon::today();
        $oneWeekLater = Carbon::today()->addWeek();

        // ----------------------------------------------------
        // 📦 グループ内データ取得
        // ----------------------------------------------------
        $expiredItems = Item::where('group_id', $groupId)
            ->whereDate('expiration_date', '<', $today)
            ->get();

        $nearExpiredItems = Item::where('group_id', $groupId)
            ->whereDate('expiration_date', '>=', $today)
            ->whereDate('expiration_date', '<=', $oneWeekLater)
            ->get();

        $memos = Memo::with(['item', 'user'])
            ->where('group_id', $groupId)
            ->latest()
            ->take(10)
            ->get();

        // ----------------------------------------------------
        // 📣 通知生成
        // ----------------------------------------------------
        $notifications = [];

        // 在庫少ない通知
        if ($user->notify_low_stock ?? false) {
            $lowStockItems = Item::where('group_id', $groupId)
                ->where('quantity', '<', $threshold)
                ->get();

            if ($lowStockItems->count() > 0) {
                $names = $lowStockItems->pluck('item')->take(3)->implode('、');
                $notifications[] = "在庫が{$threshold}個未満になっています：{$names}";
            }
        }

        // システム通知
        if ($user->notify_system ?? false) {
            $notifications[] = "【企業向け】システムメンテナンスは 10月22日 03:00〜04:00 に予定されています。";
        }

        // ----------------------------------------------------
        // 🖥️ ビューへデータ送信
        // ----------------------------------------------------
        return view('dashboard.company', compact(
            'expiredItems',
            'nearExpiredItems',
            'memos',
            'notifications',
            'threshold',
            'groupId'
        ));
    }
}
