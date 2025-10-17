<?php

namespace App\Http\Controllers;

use App\Models\PurchaseList;
use Illuminate\Http\Request;

class PurchaseListController extends Controller
{
    /**
     * 購入予定品一覧を表示
     */
    public function index()
    {
        $lists = PurchaseList::orderBy('created_at', 'desc')->get();
        return view('purchase_lists.index', compact('lists'));
    }

    /**
     * 新しい購入予定品を登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'purchase_date' => 'nullable|date',
        ]);

        PurchaseList::create($validated);

        // ✅ トースト通知を表示
        return redirect()
            ->route('purchase_lists.index')
            ->with('success', '購入リストに商品を追加しました！');
    }

    /**
     * 購入予定品を削除
     */
    public function destroy(PurchaseList $purchaseList)
    {
        $purchaseList->delete();

        // ✅ 削除後にもトースト通知を表示
        return redirect()
            ->route('purchase_lists.index')
            ->with('success', '削除しました。');
    }
}
