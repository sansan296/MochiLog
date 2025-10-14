<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();

            // 🔹 各タグがどのアイテムに属するかを示す外部キー
            $table->foreignId('item_id')
                  ->constrained()              // itemsテーブルのidを参照
                  ->onDelete('cascade');       // アイテム削除時にタグも削除

            // 🔹 タグ名
            $table->string('name');

            // 🔹 同一アイテム内での重複タグを禁止（item_id + name の組み合わせ）
            $table->unique(['item_id', 'name']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
