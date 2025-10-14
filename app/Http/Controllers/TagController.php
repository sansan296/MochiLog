<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'name' => ['required', 'string', 'max:50'],
        ]);

        // 同じアイテム内で同名タグが重複しないようチェック
        $exists = Tag::where('item_id', $validated['item_id'])
                    ->where('name', $validated['name'])
                    ->exists();
        if ($exists) {
            return response()->json(['message' => '同じタグが既に存在します。'], 422);
        }

        $tag = Tag::create($validated);
        return response()->json($tag, 201);
    }



    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['ok' => true]);
    }
}