<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
{
    if (\Illuminate\Support\Facades\Schema::hasTable('audit_logs')) {
        // 既に存在するなら何もしない（このマイグレーションは「実行済み」扱いになる）
        return;
    }

    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('action', 20);
        $table->string('target_type');
        $table->unsignedBigInteger('target_id');
        $table->json('changes')->nullable();
        $table->string('ip', 45)->nullable();
        $table->timestamps();
        $table->index(['target_type', 'target_id']);
    });
}
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
