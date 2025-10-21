<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-gray-100 leading-tight">
      🧾 監査ログ
    </h2>
  </x-slot>

  <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- 🔍 検索フォーム --}}
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

        {{-- 対象モデル --}}
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">対象</label>
          <select name="target_type" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
            <option value="">すべて</option>
            <option value="App\Models\Item" {{ request('target_type') == 'App\Models\Item' ? 'selected' : '' }}>在庫アイテム</option>
            <option value="App\Models\User" {{ request('target_type') == 'App\Models\User' ? 'selected' : '' }}>ユーザー</option>
          </select>
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
          <div class="flex justify-between items-center mb-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('Y-m-d H:i') }}</span>
              <span class="text-sm text-gray-700 dark:text-gray-200 font-medium">
                👤 {{ $log->user->name ?? 'ゲスト / 不明' }}
              </span>
            </div>
            <span class="text-xs px-3 py-1 rounded-full 
                         {{ $log->action === 'created' ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' : '' }}
                         {{ $log->action === 'updated' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-700 dark:text-yellow-100' : '' }}
                         {{ $log->action === 'deleted' ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : '' }}">
              {{ strtoupper($log->action) }}
            </span>
          </div>

          {{-- 対象名 --}}
          <p class="text-gray-700 dark:text-gray-200 text-sm mb-1">
            対象：<span class="font-semibold">
              @if ($log->target && property_exists($log->target, 'item'))
                {{ $log->target->item }}
              @elseif ($log->target && property_exists($log->target, 'name'))
                {{ $log->target->name }}
              @else
                {{ class_basename($log->target_type) }}
              @endif
            </span>
          </p>

          {{-- 変更概要 --}}
          @php
            $changes = $log->changes ?? [];
          @endphp

          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mt-2 text-sm">
            @if(!empty($changes))
              <ul class="space-y-1">
                @foreach($changes as $field => $value)
                  @if(is_array($value) && isset($value['old']) && isset($value['new']))
                    <li class="text-gray-700 dark:text-gray-200">
                      <span class="font-semibold">{{ $field }}：</span>
                      「{{ $value['old'] ?? '（なし）' }}」→「{{ $value['new'] ?? '（なし）' }}」
                    </li>
                  @endif
                @endforeach
              </ul>
            @else
              <p class="text-gray-500 dark:text-gray-300 text-sm">変更内容はありません。</p>
            @endif
          </div>

          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">IP: {{ $log->ip }}</p>
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
