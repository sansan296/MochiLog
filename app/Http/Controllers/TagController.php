<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;

class TagController extends Controller
{
    /**
     * タグ一覧をJSONで返す
     */
    public function index()
    {
          // item_id が null（全体タグ）のみ取得
        return response()->json(Tag::whereNull('item_id')->orderBy('id')->get());
    }

    /**
     * タグを作成または商品に紐付け
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|exists:items,id',
        ]);

        $tag = Tag::create([
            'name' => $validated['name'],
            'item_id' => $validated['item_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'tag' => $tag,
        ]);
    }



    /**
     * タグ名を更新（重複名も許可）
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->json()->all();

            $validated = validator($data, [
                'name' => 'required|string|max:255', // ← unique制約を削除
            ])->validate();

            $tag = Tag::findOrFail($id);
            $tag->update(['name' => $validated['name']]);

            return response()->json(['success' => true, 'tag' => $tag]);
        } catch (\Throwable $e) {
            \Log::error('Tag update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * タグ削除
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['message' => 'タグを削除しました。']);
    }
}
