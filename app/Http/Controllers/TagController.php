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
        // JSON対応
        $data = $request->json()->all();

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|exists:items,id',
        ])->validate();

        $tag = Tag::create([
            'name' => $validated['name'],
            'item_id' => $validated['item_id'] ?? null,
        ]);

        // フロントがfetchで受け取るのでJSON返却
        return response()->json($tag, 201);
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
