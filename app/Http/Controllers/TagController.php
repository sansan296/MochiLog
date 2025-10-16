<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->json()->all();

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|exists:items,id',
        ])->validate();

        // タグ作成または既存取得
        $tag = Tag::firstOrCreate(['name' => $validated['name']]);

        // 商品への紐付け
        if (!empty($validated['item_id'])) {
            $item = Item::find($validated['item_id']);
            if ($item) {
                $item->tags()->syncWithoutDetaching([$tag->id]);
            }
        }

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    // ✅ 修正版 update（JSON対応・重複対応・例外処理付き）
    public function update(Request $request, $id)
    {
        try {
            $data = $request->json()->all();

            $validated = validator($data, [
                'name' => 'required|string|max:255|unique:tags,name,' . $id,
            ])->validate();

            $tag = Tag::findOrFail($id);
            $tag->update(['name' => $validated['name']]);

            return response()->json(['success' => true, 'tag' => $tag]);
        } catch (\Throwable $e) {
            \Log::error('Tag update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['message' => 'タグを削除しました。']);
    }
}
