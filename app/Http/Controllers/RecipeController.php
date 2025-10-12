<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Item;

class RecipeController extends Controller
{
    public function index()
    {
        // 在庫から材料名を取得
        $items = Item::pluck('item')->toArray();

        // 材料を英語に翻訳
        $translatedIngredients = [];
        foreach ($items as $ingredient) {
            $response = Http::asForm()->post('https://api-free.deepl.com/v2/translate', [
                'auth_key' => env('DEEPL_API_KEY'),
                'text' => $ingredient,
                'target_lang' => 'EN',
            ]);

            $translatedIngredients[] = $response['translations'][0]['text'] ?? $ingredient;
        }

        // Spoonacular API にリクエスト
        $query = implode(',', $translatedIngredients);
        $recipes = Http::get('https://api.spoonacular.com/recipes/findByIngredients', [
            'apiKey' => env('SPOONACULAR_API_KEY'),
            'ingredients' => $query,
            'number' => 5, // 最大5件
        ])->json();

        // レシピ名を日本語に翻訳
        foreach ($recipes as &$recipe) {
            $translatedTitle = Http::asForm()->post('https://api-free.deepl.com/v2/translate', [
                'auth_key' => env('DEEPL_API_KEY'),
                'text' => $recipe['title'],
                'target_lang' => 'JA',
            ]);

            $recipe['translated_title'] = $translatedTitle['translations'][0]['text'] ?? $recipe['title'];
        }

        return view('recipes.index', compact('recipes'));
    }
}
