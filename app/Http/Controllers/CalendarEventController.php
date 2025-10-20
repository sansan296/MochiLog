<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CalendarEvent;
use App\Models\Item;
use Carbon\Carbon;

class CalendarEventController extends Controller
{
    /**
     * カレンダービュー
     */
    public function index()
    {
        $today = Carbon::today();
        $todayEvents = CalendarEvent::with('item')
            ->whereDate('date', $today)
            ->where('user_id', Auth::id())
            ->orderBy('date')
            ->get();

        return view('calendar.index', compact('todayEvents'));
    }

    /**
     * JSONでイベント一覧取得
     */
    public function fetch()
    {
        $events = CalendarEvent::with('item')
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                // ✅ item_name も考慮してタイトルを生成
                'title' => "{$e->type}：" . ($e->item->item ?? $e->item_name ?? '未指定') . "（{$e->quantity}）",
                'start' => $e->date->toDateString(),
                'color' => $e->type === '入庫' ? '#16a34a' : '#3b82f6',
                'extendedProps' => [
                    'status' => $e->status,
                    'notes' => $e->notes,
                ],
            ]);

        return response()->json($events);
    }

    /**
    * 特定日の予定をJSONで取得
    */
    public function getByDate(Request $request)
    {
        $date = $request->input('date');
        if (!$date) {
            return response()->json(['error' => '日付が指定されていません'], 400);
        }

        $events = CalendarEvent::with('item')
            ->where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->orderBy('date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'name' => $e->item->item ?? $e->item_name ?? '未指定',
                'quantity' => $e->quantity,
                'notes' => $e->notes,
                'status' => $e->status,
            ]);

        return response()->json($events);
    }




    /**
     * 新規登録
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|in:入庫,出庫',
        'date' => 'required|date',
        'quantity' => 'nullable|integer|min:1',
        'item_name' => 'nullable|string|max:255', // ✅ 自由入力
        'item_id' => 'nullable|exists:items,id',
        'notes' => 'nullable|string|max:255',
    ]);

    $validated['user_id'] = Auth::id();

    // ✅ 「出庫」で item_id が指定されていない場合、名前から在庫を特定
    if ($validated['type'] === '出庫' && empty($validated['item_id']) && !empty($validated['item_name'])) {
        $matchedItems = Item::where('item', $validated['item_name'])->get();

        if ($matchedItems->count() > 1) {
            return response()->json([
                'success' => false,
                'error' => '同名の在庫が複数あります。どれを出庫するか選択してください。',
                'options' => $matchedItems->pluck('id', 'item')
            ], 409); // 409 Conflict
        } elseif ($matchedItems->count() === 1) {
            $validated['item_id'] = $matchedItems->first()->id;
        }
    }

    CalendarEvent::create($validated);

    return response()->json(['success' => true]);
}


    /**
     * ドラッグで日付変更
     */
    public function update(Request $request, CalendarEvent $event)
    {
        $request->validate(['date' => 'required|date']);
        $event->update(['date' => $request->date]);
        return response()->json(['success' => true]);
    }

    /**
     * 削除
     */
    public function destroy(CalendarEvent $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

    public function history()
    {
        $completedEvents = CalendarEvent::with('item')
            ->where('user_id', Auth::id())
            ->where('status', '完了')
            ->orderByDesc('date')
            ->get();

        return view('calendar.history', compact('completedEvents'));
    }

}
