<?php

namespace App\Http\Controllers;

use App\Models\PurchaseList;
use Illuminate\Http\Request;

class PurchaseListController extends Controller
{
    public function index()
    {
        $lists = PurchaseList::orderBy('created_at', 'desc')->get();
        return view('purchase_lists.index', compact('lists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'purchase_date' => 'nullable|date',
        ]);

        PurchaseList::create($validated);
        return redirect()->route('purchase_lists.index')->with('success', '商品を追加しました。');
    }

    public function destroy(PurchaseList $purchaseList)
    {
        $purchaseList->delete();
        return redirect()->route('purchase_lists.index')->with('success', '削除しました。');
    }
}
