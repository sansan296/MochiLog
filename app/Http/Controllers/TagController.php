<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

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

        // ① タグを重複チェック付きで取得または作成
        $tag = \App\Models\Tag::firstOrCreate(['name' => $validated['name']]);

        // ② item_id が指定されている場合は中間テーブルに関連付け
        if (!empty($validated['item_id'])) {
            $item = \App\Models\Item::find($validated['item_id']);
            $item->tags()->syncWithoutDetaching([$tag->id]);
        }

        return response()->json(['success' => true, 'tag' => $tag]);
    }


    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $tag = Tag::findOrFail($id);
        $tag->update(['name' => $request->name]);
        return response()->json(['success' => true]);
    }


    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'タグを削除しました。']);
    }
}
