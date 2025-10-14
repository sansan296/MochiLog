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
            'name' => ['required','string','max:50','unique:tags,name'],
        ]);
        $tag = Tag::create($validated);
        return response()->json($tag, 201);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['ok' => true]);
    }
}