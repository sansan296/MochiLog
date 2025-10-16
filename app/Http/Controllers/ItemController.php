<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * 在庫一覧ページ
     * - JSONリクエスト時：Alpine.jsが使用（tags付きで全件返す）
     * - 通常リクエスト時：Bladeでページ表示
     */
    public function index(Request $request)
    {
        // ✅ JSONリクエスト（Alpine.js 側からの fetch）
        if ($request->boolean('json')) {
            $items = Item::with(['tags', 'user'])
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($item) {
                    // ✅ アニメーション用の一意キー
                    $item->fade_key = uniqid('fade_');
                    return $item;
                });

            return response()->json($items);
        }

        // ✅ 通常HTML表示（Blade）
        $items = Item::with(['tags', 'user'])
            ->orderBy('id', 'desc')
            ->paginate(12);

        $totalQuantity = $items->sum('quantity');

        return view('items.index', compact('items', 'totalQuantity'));
    }

    /**
     * 在庫登録フォーム表示
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * 在庫登録処理
     */
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

        // ✅ 賞味期限を組み立て
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

    /**
     * 詳細ページ
     */
    public function show($id)
    {
        $item = Item::with(['user', 'memos', 'tags'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * 編集ページ
     */
    public function edit($id)
    {
        $item = Item::with(['tags'])->findOrFail($id);

        $expiration = ['year' => null, 'month' => null, 'day' => null];
        if ($item->expiration_date) {
            $expiration['year'] = $item->expiration_date->format('Y');
            $expiration['month'] = $item->expiration_date->format('m');
            $expiration['day'] = $item->expiration_date->format('d');
        }

        return view('items.edit', compact('item', 'expiration'));
    }
}
