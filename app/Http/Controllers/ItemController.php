<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // JSONリクエストの場合（Alpine.js が fetch で呼ぶ）
        if ($request->boolean('json')) {
            $items = Item::with(['tags', 'user'])
                ->orderBy('id', 'desc')
                ->get();
            return response()->json($items);
        }

        // 通常のページ表示（従来の Blade）
        $items = Item::with(['tags', 'user'])
            ->orderBy('id', 'desc')
            ->paginate(12);

        $totalQuantity = $items->sum('quantity');

        return view('items.index', compact('items', 'totalQuantity'));
    }

    public function create()
    {
        // 登録用フォームを表示
        return view('items.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'expiration_year' => 'nullable|integer|min:2024|max:2100',
            'expiration_month' => 'nullable|integer|min:1|max:12',
            'expiration_day' => 'nullable|integer|min:1|max:31',
        ]);

        $item = new Item();
        $item->item = $validated['item'];
        $item->quantity = $validated['quantity'];

        // 賞味期限が指定されていた場合
        if ($request->filled(['expiration_year', 'expiration_month', 'expiration_day'])) {
            $item->expiration_date = sprintf(
                '%04d-%02d-%02d',
                $validated['expiration_year'],
                $validated['expiration_month'],
                $validated['expiration_day']
            );
        }

        $item->user_id = auth()->id();
        $item->save();

        return redirect()->route('items.index')
            ->with('success', '在庫を追加しました。');
    }

    public function show($id)
    {
        $item = \App\Models\Item::with(['user', 'memos', 'tags'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function edit($id)
    {
        $item = \App\Models\Item::with(['tags'])->findOrFail($id);

        // 賞味期限を分解（年・月・日をフォーム初期値に）
        $expiration = ['year' => null, 'month' => null, 'day' => null];
        if ($item->expiration_date) {
            $expiration['year'] = $item->expiration_date->format('Y');
            $expiration['month'] = $item->expiration_date->format('m');
            $expiration['day'] = $item->expiration_date->format('d');
        }

        return view('items.edit', compact('item', 'expiration'));
    }

}
