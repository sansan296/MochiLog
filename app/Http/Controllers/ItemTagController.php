<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;

class ItemTagController extends Controller
{
    // アイテムごとのタグ一覧
    public function index(Item $item)
    {
        $tags = Tag::orderBy('name')->get()
            ->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'checked' => $item->tags->contains($tag->id),
            ]);

        return response()->json($tags);
    }

    // タグの付与・削除トグル
    public function toggle(Item $item, Request $request)
    {
        $data = $request->validate([
            'tag_id' => ['required', 'exists:tags,id'],
            'checked' => ['required', 'boolean'],
        ]);

        if ($data['checked']) {
            $item->tags()->syncWithoutDetaching([$data['tag_id']]);
        } else {
            $item->tags()->detach($data['tag_id']);
        }

        return response()->json(['ok' => true]);
    }
}
