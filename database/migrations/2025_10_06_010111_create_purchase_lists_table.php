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
        Schema::create('purchase_lists', function (Blueprint $table) {
            $table->id();
            $table->string('item'); // 商品名
            $table->integer('quantity')->nullable(); // 個数
            $table->date('purchase_date')->nullable(); // 購入日
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_lists');
    }
};
