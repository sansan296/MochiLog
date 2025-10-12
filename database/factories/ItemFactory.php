<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $foods = [
    // 果物
    'りんご', 'バナナ', 'オレンジ', 'ぶどう', 'イチゴ', 'メロン', 'スイカ', 'レモン', 'パイナップル', 'マンゴー',
    'キウイ', '桃', '梨', 'さくらんぼ', 'みかん', '柿', 'ブルーベリー', 'ラズベリー', 'グレープフルーツ', 'アボカド',

    // 野菜
    'トマト', 'きゅうり', 'キャベツ', 'レタス', 'にんじん', 'じゃがいも', 'さつまいも', 'かぼちゃ', '玉ねぎ', 'ピーマン',
    'なす', 'ほうれん草', 'ブロッコリー', 'アスパラガス', '大根', 'ごぼう', 'レンコン', 'セロリ', '白菜', 'ししとう',

    // 肉・卵・乳製品
    '牛肉', '豚肉', '鶏肉', 'ラム肉', 'ベーコン', 'ハム', 'ソーセージ', '卵', '牛乳', 'チーズ',
    'ヨーグルト', 'バター', 'クリーム', '鴨肉', 'ターキー',

    // 魚介類
    '鮭', 'まぐろ', 'いか', 'たこ', 'えび', 'かに', 'さんま', 'さば', 'いわし', 'ほたて',
    'あじ', 'ひらめ', 'うなぎ', 'しじみ', 'あさり',

    // 豆・穀物・その他
    '大豆', '枝豆', '納豆', '豆腐', '小豆', '黒豆', 'ごま', '米', '小麦粉', 'パン',
    'パスタ', 'うどん', 'そば', 'カレー粉', 'しょうゆ',
    'みそ', '塩', '砂糖', 'こしょう', '酢'
];


        return [
            'user_id' => User::factory(),
            'item' => $this->faker->randomElement($foods)
        ];
    }
}
