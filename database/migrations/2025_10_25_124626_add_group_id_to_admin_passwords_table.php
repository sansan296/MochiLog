<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_passwords', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_passwords', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('id');
            }
        });

        // ✅ groupsテーブルが空の場合、仮のユーザーとグループを作成
        if (DB::table('groups')->count() === 0) {
            // 仮のユーザーを作成
            $userId = DB::table('users')->insertGetId([
                'name' => 'System User',
                'email' => 'system@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 仮のグループを作成
            DB::table('groups')->insert([
                'name' => 'Default Group',
                'mode' => '家庭',
                'user_id' => $userId, // ✅ user_id を設定
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ✅ 既存のadmin_passwordsに仮のgroup_idを割り当て
        if (Schema::hasColumn('admin_passwords', 'group_id')) {
            DB::table('admin_passwords')->whereNull('group_id')->update(['group_id' => 1]);
        }

        // ✅ 外部キー追加（groups.idを参照）
        Schema::table('admin_passwords', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('admin_passwords', function (Blueprint $table) {
            if (Schema::hasColumn('admin_passwords', 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }
        });
    }
};
