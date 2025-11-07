<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-white leading-tight">
            管理者設定ページ
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- ✅ フラッシュメッセージ --}}
        @if (session('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 px-4 py-3 rounded-xl shadow mb-6 text-center text-sm sm:text-base">
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 px-4 py-3 rounded-xl shadow mb-6 text-center text-sm sm:text-base">
            {{ session('error') }}
        </div>
        @endif

        {{-- 👤 現在ログイン中ユーザー情報 --}}
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-md mb-6">
            <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">
                現在ログイン中：
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span> さん（
                @if(auth()->user()->is_admin)
                <span class="text-indigo-600 dark:text-indigo-300 font-semibold">管理者</span>
                @else
                <span class="text-gray-500 dark:text-gray-400 font-semibold">一般ユーザー</span>
                @endif
                ）
            </p>
        </div>

        {{-- 📋 ユーザー一覧 --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-x-auto border border-gray-100 dark:border-gray-700 mb-10">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">名前</th>
                        <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">メール</th>
                        <th class="px-4 sm:px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">権限</th>
                        <th class="px-4 sm:px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">操作</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-4 sm:px-6 py-3 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                        <td class="px-4 sm:px-6 py-3 text-center">
                            @if ($user->is_admin)
                            <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-100 rounded-full">管理者</span>
                            @else
                            <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-100 rounded-full">一般</span>
                            @endif
                        </td>

                        <td class="px-4 sm:px-6 py-3 text-center">
                            {{-- ✅ 各ユーザーの状態に応じてボタンを切替 --}}
                            <form method="POST"
                                action="{{ $user->id === auth()->id() 
          ? route('admin.toggle.self') 
          : route('admin.toggle.user', ['user' => $user->id]) }}">
                                @csrf

                                @if ($user->is_admin)
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow transition">
                                    一般ユーザーに戻す
                                </button>
                                @else
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
                                    管理者に設定
                                </button>
                                @endif
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>