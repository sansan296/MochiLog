<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 家庭用 or 企業用
            $table->enum('user_type', ['household','enterprise'])->default('household');

            // 家庭用フィールド
            $table->enum('gender', ['男性','女性','その他'])->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('occupation')->nullable();

            // 企業用フィールド
            $table->string('contact_email')->nullable(); // 企業向けの連絡先（users.emailとは別管理）
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('position')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('profiles');
    }
};
