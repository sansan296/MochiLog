<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 実行：recipe_bookmarksテーブルを作成
     */
    public function up(): void
    {
        Schema::create('recipe_bookmarks', function (Blueprint $table) {
            $table->id();

            // 🔐 ユーザーとの紐付け（ユーザー削除時にブックマークも削除）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 🍳 Spoonacularのレシピ情報
            $table->string('recipe_id');          // API上の一意ID
            $table->string('title');              // 英語タイトル
            $table->string('translated_title')->nullable(); // 日本語タイトル（DeepL翻訳）
            $table->string('image_url')->nullable();         // サムネイル画像URL

            $table->timestamps();

            // 🔎 ユーザーごとに同じレシピを重複登録できないよう制約
            $table->unique(['user_id', 'recipe_id']);
        });
    }

    /**
     * ロールバック処理
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_bookmarks');
    }
};
