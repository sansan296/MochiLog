<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-gray-100 leading-tight">
      🧾 監査ログ
    </h2>
  </x-slot>

  <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- 🔍 検索フォーム（簡略版） --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg mb-8">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
        🔎 検索フィルター
      </h3>

      <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- 操作内容 --}}
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">操作内容</label>
          <select name="action" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
            <option value="">すべて</option>
            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>作成</option>
            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>更新</option>
            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>削除</option>
          </select>
        </div>

        {{-- ユーザー名 --}}
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">ユーザー名</label>
          <input type="text" name="user_name" placeholder="例: 田中太郎"
                 value="{{ request('user_name') }}"
                 class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
        </div>

        {{-- 日付範囲 --}}
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">開始日</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}"
                 class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
        </div>
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">終了日</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}"
                 class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
        </div>

        {{-- 検索・リセット --}}
        <div class="sm:col-span-3 flex justify-end mt-2">
          <button type="submit"
                  class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
            検索
          </button>
          <a href="{{ route('audit-logs.index') }}"
             class="ml-3 px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 rounded-lg transition">
            リセット
          </a>
        </div>
      </form>
    </div>

    {{-- 🧾 ログ一覧 --}}
    <div class="space-y-4">
      @forelse($logs as $log)
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow hover:shadow-lg transition">

          {{-- 上部情報 --}}
          <div class="flex justify-between items-center mb-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('Y-m-d H:i') }}</span>
              <span class="text-sm text-gray-700 dark:text-gray-200 font-medium">
                👤 {{ $log->user->name ?? 'ゲスト / 不明' }}
              </span>
            </div>

            {{-- アクションバッジ（日本語化） --}}
            @php
              $actionLabel = match($log->action) {
                'created' => '作成',
                'updated' => '更新',
                'deleted' => '削除',
                default => strtoupper($log->action)
              };
            @endphp
            <span class="text-xs px-3 py-1 rounded-full 
                         {{ $log->action === 'created' ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' : '' }}
                         {{ $log->action === 'updated' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-700 dark:text-yellow-100' : '' }}
                         {{ $log->action === 'deleted' ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : '' }}">
              {{ $actionLabel }}
            </span>
          </div>

          {{-- 対象名（商品名やユーザー名） --}}
          <p class="text-gray-700 dark:text-gray-200 text-sm mb-1">
            対象：
            <span class="font-semibold">
              @if ($log->target && property_exists($log->target, 'item'))
                {{ $log->target->item }}
              @elseif ($log->target && property_exists($log->target, 'name'))
                {{ $log->target->name }}
              @else
                {{ class_basename($log->target_type) }}
              @endif
            </span>
          </p>

          {{-- 折りたたみ式詳細 --}}
@php
    // JSON文字列なら配列に変換
    $rawChanges = $log->changes ?? [];
    if (is_string($rawChanges)) {
        $changes = json_decode($rawChanges, true) ?? [];
    } else {
        $changes = $rawChanges;
    }

    // Laravel Auditing パッケージ系の対応
    if (isset($changes['old_values']) || isset($changes['new_values'])) {
        $old = $changes['old_values'] ?? [];
        $new = $changes['new_values'] ?? [];
        $diffs = [];
        foreach ($new as $key => $value) {
            $diffs[$key] = [
                'old' => $old[$key] ?? '（なし）',
                'new' => $value ?? '（なし）',
            ];
        }
        $changes = $diffs;
    }
@endphp

{{-- 折りたたみ式詳細（ユーザー向けに整形） --}}
@php
    // JSONを配列化
    $rawChanges = $log->changes ?? [];
    if (is_string($rawChanges)) {
        $changes = json_decode($rawChanges, true) ?? [];
    } else {
        $changes = $rawChanges;
    }

    // Before/After両方がある場合の抽出
    $before = $changes['before'] ?? ($changes['old_values'] ?? []);
    $after  = $changes['after'] ?? ($changes['new_values'] ?? []);

    // JSON文字列の中にBefore/Afterが埋め込まれている場合
    if (empty($before) && empty($after)) {
        // "Before："や"After："が含まれている形式をパース
        if (is_string($rawChanges) && str_contains($rawChanges, 'Before：')) {
            preg_match('/Before：\s*(\{.*\})/u', $rawChanges, $bMatch);
            preg_match('/After：\s*(\{.*\})/u', $rawChanges, $aMatch);
            $before = isset($bMatch[1]) ? json_decode($bMatch[1], true) : [];
            $after  = isset($aMatch[1]) ? json_decode($aMatch[1], true) : [];
        }
    }

    // 差分を抽出
    $diffs = [];
    if (!empty($after)) {
        foreach ($after as $key => $newVal) {
            $oldVal = $before[$key] ?? null;
            if ($oldVal != $newVal) {
                $diffs[$key] = [
                    'old' => $oldVal ?? '（なし）',
                    'new' => $newVal ?? '（なし）'
                ];
            }
        }
    }
@endphp

<details class="mt-3 group">
  <summary
    class="cursor-pointer flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2 text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
    <span>📝 変更内容を表示</span>
    <span class="text-gray-500 dark:text-gray-400 group-open:rotate-180 transition-transform">▼</span>
  </summary>

  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mt-2 text-sm border border-gray-200 dark:border-gray-600">
    @if(!empty($diffs))
      <ul class="space-y-1">
        @foreach($diffs as $field => $value)
          @php
            // 表示名（フィールド名を日本語化）
            $labelMap = [
              'item' => '商品名',
              'quantity' => '数量',
              'unit' => '単位',
              'expiration_date' => '賞味期限',
              'best_before' => '消費期限',
              'updated_at' => '更新日時',
              'created_at' => '作成日時',
              'user_id' => '登録者',
            ];
            $label = $labelMap[$field] ?? $field;
          @endphp
          <li class="text-gray-700 dark:text-gray-200">
            <span class="font-semibold">{{ $label }}：</span>
            @if($value['old'] === $value['new'])
              <span class="text-gray-500">（変更なし）</span>
            @else
              <span class="text-red-500 line-through">{{ $value['old'] ?? '（なし）' }}</span>
              →
              <span class="text-green-600 font-medium">{{ $value['new'] ?? '（なし）' }}</span>
            @endif
          </li>
        @endforeach
      </ul>
    @else
      <p class="text-gray-500 dark:text-gray-300 text-sm">変更内容は記録されていません。</p>
    @endif
  </div>
</details>


        </div>
      @empty
        <p class="text-center text-gray-500 dark:text-gray-400 mt-6">ログがありません。</p>
      @endforelse
    </div>

    {{-- ページネーション --}}
    <div class="mt-6">
      {{ $logs->withQueryString()->links() }}
    </div>

  </div>
</x-app-layout>
