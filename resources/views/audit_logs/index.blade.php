<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">操作履歴</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="mb-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <form class="flex gap-3" method="GET">
                <input class="border rounded px-2 py-1" type="text" name="action" placeholder="action (created/updated/deleted)" value="{{ request('action') }}">
                <input class="border rounded px-2 py-1" type="text" name="target_type" placeholder="App\Models\Item" value="{{ request('target_type') }}">
                <input class="border rounded px-2 py-1" type="text" name="target_id" placeholder="target_id" value="{{ request('target_id') }}">
                <x-primary-button>検索</x-primary-button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3">日時</th>
                        <th class="text-left py-2 px-3">ユーザー</th>
                        <th class="text-left py-2 px-3">アクション</th>
                        <th class="text-left py-2 px-3">対象</th>
                        <th class="text-left py-2 px-3">変更</th>
                        <th class="text-left py-2 px-3">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-b align-top">
                            <td class="py-2 px-3">{{ $log->created_at }}</td>
                            <td class="py-2 px-3">{{ $log->user->name ?? 'ゲスト/不明' }}</td>
                            <td class="py-2 px-3">{{ $log->action }}</td>
                            <td class="py-2 px-3">
                                {{ class_basename($log->target_type) }}#{{ $log->target_id }}
                            </td>
                            <td class="py-2 px-3">
<pre class="whitespace-pre-wrap text-xs">{{ json_encode($log->changes, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                            </td>
                            <td class="py-2 px-3">{{ $log->ip }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
