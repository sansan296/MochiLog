<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // 操作ユーザー
            $table->string('action'); // created / updated / deleted
            $table->string('target_type'); // App\Models\Item など
            $table->unsignedBigInteger('target_id'); // 対象ID
            $table->json('changes')->nullable(); // {"before": {...}, "after": {...}}
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
