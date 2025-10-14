<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">管理者ダッシュボード</h2>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <x-primary-button>ログアウト</x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <p class="text-gray-700 dark:text-gray-200">ここに管理者専用の機能を並べていくよ。</p>
        </div>
    </div>
</x-app-layout>
