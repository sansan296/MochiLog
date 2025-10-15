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
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        $tag = Tag::create(['name' => $request->name]);

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
