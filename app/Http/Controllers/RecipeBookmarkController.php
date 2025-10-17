<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeBookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RecipeBookmarkController extends Controller
{
    /**
     * ブックマーク一覧ページを表示
     */
    public function index()
    {
        $bookmarks = RecipeBookmark::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('recipes.bookmarks', compact('bookmarks'));
    }

    /**
     * ブックマーク登録（DeepLでタイトルを翻訳）
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'recipe_id' => 'required|string|max:255',
        'title' => 'required|string|max:255',
        'image_url' => 'nullable|string|max:255',
    ]);

    // 🇯🇵 DeepL翻訳（英語タイトル → 日本語）
    $translatedTitle = $validated['title'];
    try {
        $response = Http::asForm()->post('https://api-free.deepl.com/v2/translate', [
            'auth_key' => env('DEEPL_API_KEY'),
            'text' => $validated['title'],
            'target_lang' => 'JA',
        ]);
        $data = $response->json();
        if (isset($data['translations'][0]['text'])) {
            $translatedTitle = $data['translations'][0]['text'];
        }
    } catch (\Throwable $e) {
        logger('DeepL翻訳失敗: ' . $e->getMessage());
    }

    // ✅ 既に存在する場合も翻訳タイトルを更新
    RecipeBookmark::updateOrCreate(
        [
            'user_id' => Auth::id(),
            'recipe_id' => $validated['recipe_id'],
        ],
        [
            'title' => $validated['title'],
            'translated_title' => $translatedTitle,
            'image_url' => $validated['image_url'] ?? null,
        ]
    );

    return back()->with('message', 'ブックマークに追加しました！');
}

    /**
     * ブックマーク削除
     */
    public function destroy($id)
    {
        RecipeBookmark::where('user_id', Auth::id())
            ->where('recipe_id', $id)
            ->delete();

        return back()->with('message', 'ブックマークを削除しました。');
    }
}
