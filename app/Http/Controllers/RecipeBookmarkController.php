<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeBookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecipeBookmarkController extends Controller
{
    /**
     * コンストラクタ：ログインユーザーのみアクセス可能
     */
    public function __construct()
    {
        // ✅ 親のコンストラクタ呼び出しは不要！
        $this->middleware('auth');
    }

    /**
     * 🔖 ブックマーク一覧ページを表示
     */
    public function index()
    {
        try {
            $bookmarks = RecipeBookmark::where('user_id', Auth::id())
                ->latest()
                ->get();

            return view('recipes.bookmarks', compact('bookmarks'));
        } catch (\Exception $e) {
            Log::error('ブックマーク一覧取得エラー: ' . $e->getMessage());
            return back()->with('error', 'ブックマーク一覧の取得に失敗しました。');
        }
    }

    /**
     * ⭐ ブックマークを登録（重複時はスキップ）
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        try {
            RecipeBookmark::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'recipe_id' => $validated['recipe_id'],
                ],
                [
                    'title' => $validated['title'],
                    'image_url' => $validated['image_url'] ?? null,
                ]
            );

            return back()->with('message', 'ブックマークに追加しました！');
        } catch (\Exception $e) {
            Log::error('ブックマーク登録エラー: ' . $e->getMessage());
            return back()->with('error', 'ブックマークの登録に失敗しました。');
        }
    }

    /**
     * ❌ ブックマークを削除
     */
    public function destroy($id)
    {
        try {
            $deleted = RecipeBookmark::where('user_id', Auth::id())
                ->where('recipe_id', $id)
                ->delete();

            if ($deleted) {
                return back()->with('message', 'ブックマークを削除しました。');
            } else {
                return back()->with('error', '該当するブックマークが見つかりませんでした。');
            }
        } catch (\Exception $e) {
            Log::error('ブックマーク削除エラー: ' . $e->getMessage());
            return back()->with('error', 'ブックマークの削除に失敗しました。');
        }
    }
}
