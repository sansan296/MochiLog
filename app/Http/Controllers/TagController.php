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
        return response()->json(Tag::orderBy('id')->get());
    }

    /**
     * タグを作成または商品に紐付け
     */
    public function store(Request $request)
    {
        $data = $request->json()->all();

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|exists:items,id',
        ])->validate();

        // タグ名が重複しても作成可能に変更
        $tag = Tag::create(['name' => $validated['name']]);

        // 商品との関連付け（item_tag テーブル）
        if (!empty($validated['item_id'])) {
            $item = Item::find($validated['item_id']);
            if ($item) {
                $item->tags()->syncWithoutDetaching([$tag->id]);
            }
        }

        return response()->json(['success' => true, 'tag' => $tag]);
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
