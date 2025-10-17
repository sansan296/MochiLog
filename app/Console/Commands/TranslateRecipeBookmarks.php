<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\RecipeBookmark;

class TranslateRecipeBookmarks extends Command
{
    /**
     * コマンドのシグネチャ（実行名）
     *
     * 例: php artisan translate:bookmarks
     */
    protected $signature = 'translate:bookmarks';

    /**
     * コマンドの説明
     */
    protected $description = '既存のブックマークのタイトルをDeepLで日本語翻訳し、translated_titleに保存する';

    /**
     * 実行ロジック
     */
    public function handle(): int
    {
        $deeplUrl = env('DEEPL_API_URL', 'https://api-free.deepl.com/v2/translate');
        $deeplKey = env('DEEPL_API_KEY');

        // 対象ブックマークを取得（translated_title が NULL または空）
        $bookmarks = RecipeBookmark::whereNull('translated_title')
            ->orWhere('translated_title', '')
            ->get();

        if ($bookmarks->isEmpty()) {
            $this->info('翻訳が必要なブックマークはありません ✅');
            return Command::SUCCESS;
        }

        $this->info("翻訳対象: {$bookmarks->count()} 件のブックマークを処理中...");

        $success = 0;
        $fail = 0;

        foreach ($bookmarks as $bookmark) {
            try {
                $response = Http::asForm()->post($deeplUrl, [
                    'auth_key'    => $deeplKey,
                    'text'        => $bookmark->title,
                    'target_lang' => 'JA',
                ]);

                $data = $response->json();
                $translated = $data['translations'][0]['text'] ?? $bookmark->title;

                // DBに保存
                $bookmark->translated_title = $translated;
                $bookmark->save();

                $success++;
                $this->info("✅ 翻訳成功: {$bookmark->title} → {$translated}");
            } catch (\Throwable $e) {
                $fail++;
                $this->error("⚠️ 翻訳失敗: {$bookmark->title} ({$e->getMessage()})");
            }

            // API制限対策：0.5秒待機
            usleep(500000);
        }

        $this->newLine();
        $this->info("完了！ 成功: {$success}件 / 失敗: {$fail}件");

        return Command::SUCCESS;
    }
}
