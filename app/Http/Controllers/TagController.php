<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;

class TagController extends Controller
{
    public function index()
    {
        // 全タグをJSONで返す
        return response()->json(Tag::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
        ]);

        // タグ作成（同名があれば取得）
        $tag = Tag::firstOrCreate(['name' => $validated['name']]);

        // item_idがあれば中間テーブルに紐づけ
        if ($request->filled('item_id')) {
            $item = Item::findOrFail($request->item_id);
            $item->tags()->syncWithoutDetaching([$tag->id]);
        }

        return response()->json($tag, 201);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['message' => 'タグを削除しました。']);
    }
}
