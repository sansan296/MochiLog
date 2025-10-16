<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeBookmark;
use Illuminate\Support\Facades\Auth;

class RecipeBookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = RecipeBookmark::where('user_id', Auth::id())->get();
        return response()->json($bookmarks);
    }

        public function store(Request $request)
        {
            RecipeBookmark::firstOrCreate([
                'user_id' => Auth::id(),
                'recipe_id' => $request->recipe_id,
            ], [
                'title' => $request->title,
                'image_url' => $request->image_url,
            ]);

            return back()->with('message', 'ブックマークに追加しました！');
        }

        public function destroy($id)
        {
            RecipeBookmark::where('user_id', Auth::id())
                ->where('recipe_id', $id)
                ->delete();

            return back()->with('message', 'ブックマークを削除しました。');
        }

}

