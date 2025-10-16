<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\RecipeBookmark;

class RecipeController extends Controller
{
    /**
     * 在庫から作れるレシピ一覧を表示
     */
    public function index()
    {
        // -------------------------------------
        // 🧩 1. 在庫アイテムを取得
        // -------------------------------------
        $items = Item::pluck('item')->filter()->toArray();

        if (empty($items)) {
            return view('recipes.index', [
                'recipes' => [],
                'bookmarkedRecipeIds' => [],
                'message' => '在庫が登録されていません。',
            ]);
        }

        // -------------------------------------
        // 🌐 2. 在庫名を英語に翻訳（DeepL API）
        // -------------------------------------
        $translatedIngredients = [];

        foreach ($items as $ingredient) {
            try {
                $response = Http::asForm()->post('https://api-free.deepl.com/v2/translate', [
                    'auth_key' => env('DEEPL_API_KEY'),
                    'text' => $ingredient,
                    'target_lang' => 'EN',
                ]);

                $data = $response->json();

                if (isset($data['translations'][0]['text'])) {
                    $translatedIngredients[] = $data['translations'][0]['text'];
                } else {
                    $translatedIngredients[] = $ingredient; // 翻訳失敗時フォールバック
                }
            } catch (\Throwable $e) {
                logger('DeepL翻訳エラー', ['message' => $e->getMessage()]);
                $translatedIngredients[] = $ingredient;
            }
        }

        // -------------------------------------
        // 🍳 3. Spoonacular APIでレシピ取得
        // -------------------------------------
        $recipes = [];
        $query = implode(',', $translatedIngredients);

        try {
            $response = Http::get('https://api.spoonacular.com/recipes/findByIngredients', [
                'apiKey' => env('SPOONACULAR_API_KEY'),
                'ingredients' => $query,
                'number' => 20,
                'ranking' => 1,
            ]);

            if ($response->successful()) {
                $recipes = $response->json();
            } else {
                logger('Spoonacular API エラー', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Throwable $e) {
            logger('Spoonacular通信エラー', ['message' => $e->getMessage()]);
        }

        // -------------------------------------
        // 🇯🇵 4. レシピ名を日本語に翻訳（DeepL API）
        // -------------------------------------
        foreach ($recipes as &$recipe) {
            if (!isset($recipe['title'])) continue;

            try {
                $translated = Http::asForm()->post('https://api-free.deepl.com/v2/translate', [
                    'auth_key' => env('DEEPL_API_KEY'),
                    'text' => $recipe['title'],
                    'target_lang' => 'JA',
                ])->json();

                $recipe['translated_title'] = $translated['translations'][0]['text'] ?? $recipe['title'];
            } catch (\Throwable $e) {
                $recipe['translated_title'] = $recipe['title'];
            }
        }
        unset($recipe);

        // -------------------------------------
        // ⭐ 5. ブックマーク済みのレシピID取得
        // -------------------------------------
        $bookmarkedRecipeIds = Auth::check()
            ? RecipeBookmark::where('user_id', Auth::id())->pluck('recipe_id')->toArray()
            : [];

        // -------------------------------------
        // 🖥️ 6. 結果をビューに渡す
        // -------------------------------------
        return view('recipes.index', [
            'recipes' => $recipes,
            'bookmarkedRecipeIds' => $bookmarkedRecipeIds,
            'message' => count($recipes)
                ? null
                : '該当するレシピが見つかりませんでした。',
        ]);
    }
}
