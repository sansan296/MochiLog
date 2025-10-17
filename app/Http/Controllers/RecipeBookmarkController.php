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

        // DeepL API設定
        $deeplUrl = env('DEEPL_API_URL', 'https://api-free.deepl.com/v2/translate');
        $deeplKey = env('DEEPL_API_KEY');

        // 翻訳（キャッシュ付き）
        $translatedTitle = Cache::remember('deepl_ja_' . md5($validated['title']), 86400, function () use ($validated, $deeplUrl, $deeplKey) {
            try {
                $res = Http::asForm()->post($deeplUrl, [
                    'auth_key'    => $deeplKey,
                    'text'        => $validated['title'],
                    'target_lang' => 'JA',
                ]);

                $data = $res->json();
                return $data['translations'][0]['text'] ?? $validated['title'];
            } catch (\Throwable $e) {
                logger('DeepL翻訳エラー（Bookmark登録）', ['msg' => $e->getMessage()]);
                return $validated['title'];
            }
        });

        // 登録または既存確認
        RecipeBookmark::firstOrCreate(
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
