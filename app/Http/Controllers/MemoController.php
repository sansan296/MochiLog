<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Memo;
use Illuminate\Http\Request;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Item $item)
    {
        return view('items.memos.create', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
        'memo' => 'required|string|max:255',
    ]);

        $item->memos()->create([
        'memo' => $request->memo,
        'user_id' => auth()->id(),
    ]);

        return redirect()->route('items.show', $item);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item, Memo $memo)
    {
        return view('items.memos.show', compact('item', 'memo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item, Memo $memo)
    {
        return view('items.memos.edit', compact('item', 'memo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item, Memo $memo)
    {
        $request->validate([
        'memo' => 'required|string|max:255',
    ]);

        $memo->update($request->only('memo'));

        return redirect()->route('items.memos.show', [$item, $memo]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item, Memo $memo)
    {
        $memo->delete();

        return redirect()->route('items.show', $item);
    }
}
