<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;

class ItemTagController extends Controller
{
    // 対象アイテムに対する全タグ＋付与状態を返す
    public function list(Item $item)
    {
        $all = Tag::orderBy('name')->get(['id','name']);
        $attached = $item->tags()->pluck('tags.id')->toArray();

        $data = $all->map(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'checked' => in_array($t->id, $attached),
        ])->values();

        return response()->json($data);
    }

    // 1つのタグIDをトグル（付与/外す）
    public function toggle(Item $item, Request $request)
    {
        $validated = $request->validate([
            'tag_id' => ['required','exists:tags,id'],
            'checked' => ['required','boolean'],
        ]);
        if ($validated['checked']) {
            $item->tags()->syncWithoutDetaching([$validated['tag_id']]);
        } else {
            $item->tags()->detach($validated['tag_id']);
        }
        return response()->json(['ok' => true]);
    }
}

