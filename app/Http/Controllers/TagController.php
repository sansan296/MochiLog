<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;

class TagController extends Controller
{
    /**
     * タグ一覧をJSONで返す
     * → 全体タグ + 商品タグの両方を返す（絞り込みに必要）
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|exists:items,id',
        ]);

        // ✅ 商品に紐付けるタグの場合（item_idあり）
        if (!empty($validated['item_id'])) {
            $item = Item::findOrFail($validated['item_id']);

            // 同じ名前のタグがすでに存在するかチェック
            $tag = Tag::firstOrCreate(
                ['name' => $validated['name']],
                ['item_id' => null] // 全体タグを再利用
            );

            // 商品にタグを紐付け（中間テーブルを使用している場合）
            if (method_exists($item, 'tags')) {
                $item->tags()->syncWithoutDetaching([$tag->id]);
            }

            return response()->json([
                'success' => true,
                'tag' => $tag,
            ]);
        }

        // ✅ 全体タグとして登録（item_idなし）
        $tag = Tag::firstOrCreate(['name' => $validated['name']]);

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
                'name' => 'required|string|max:255',
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
        // 商品との紐付け解除（中間テーブルを使用している場合）
        if (method_exists($tag, 'items')) {
            $tag->items()->detach();
        }

        $tag->delete();
        return response()->json(['message' => 'タグを削除しました。']);
    }
}
