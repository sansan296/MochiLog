<!DOCTYPE html>
<html 
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  x-data="{
      darkMode: localStorage.getItem('theme') === 'dark',
      toggleTheme() {
          this.darkMode = !this.darkMode;
          localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
          document.documentElement.classList.toggle('dark', this.darkMode);
      }
  }"
  x-init="document.documentElement.classList.toggle('dark', darkMode)"
  x-bind:class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MilLog') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/MilLog_favicon_flower.svg') }}">


    <!-- 🖋 フォント -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- 🌀 Tailwind + Alpine + Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>

    <!-- ✨ Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => lucide.createIcons());
    </script>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
<div class="min-h-screen flex flex-col">

{{-- 🌐 ナビゲーションバー --}}
<nav 
    x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-50"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">

        {{-- 🔷 左側：ロゴとアプリ名 --}}
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/MilLog_favicon_flower.svg') }}" alt="MilLog" class="h-8 w-8">
            <span class="text-xl font-bold text-gray-800 dark:text-gray-100 tracking-wide">MilLog</span>
        </div>

        {{-- 🌟 右側：時刻 + ダークモード + メニュー + ログアウト --}}
        <div class="flex items-center space-x-4 sm:space-x-5">

            {{-- ⏰ 現在時刻 --}}
            <div class="text-lg sm:text-2xl font-extrabold text-blue-600 tracking-widest drop-shadow"
                x-data="{ time: '' }"
                x-init="
                    setInterval(() => {
                        const now = new Date();
                        const h = String(now.getHours()).padStart(2, '0');
                        const m = String(now.getMinutes()).padStart(2, '0');
                        time = `${h}:${m}`;
                    }, 1000);
                "
                x-text="time">
            </div>

            {{-- 🌙 ダークモード切替 --}}
            <button 
                @click="
                    darkMode = !darkMode;
                    document.documentElement.classList.toggle('dark', darkMode);
                    localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                " 
                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                title="テーマ切り替え"
            >
                <template x-if="!darkMode">
                    <i data-lucide="moon" class="w-5 h-5 text-gray-600 dark:text-yellow-300"></i>
                </template>
                <template x-if="darkMode">
                    <i data-lucide="sun" class="w-5 h-5 text-yellow-400 dark:text-yellow-200"></i>
                </template>
            </button>

            {{-- 🍔 メニュー --}}
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open" 
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                    title="メニュー"
                >
                    <i data-lucide="menu" class="w-6 h-6 text-gray-700 dark:text-gray-200"></i>
                </button>

                {{-- 📋 ドロップダウン --}}
<div 
    x-show="open"
    @click.away="open = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
    class="absolute right-0 mt-3 w-44 bg-white/70 dark:bg-gray-800/80 backdrop-blur-xl border border-white/30 dark:border-gray-700 rounded-2xl shadow-xl py-3 z-50 origin-top-right"
>
    @php
        $currentGroupId = session('selected_group_id');
    @endphp

    {{-- 🌸 メニュー一覧 --}}
    <a href="{{ route('menu.index') }}"
       class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/70 rounded-lg transition">
        <i data-lucide="grid" class="w-5 h-5"></i>
        <span>メニュー</span>
    </a>

    <a href="{{ route('settings.index') }}"
       class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/70 rounded-lg transition">
        <i data-lucide="settings" class="w-5 h-5"></i>
        <span>設定</span>
    </a>

    <a href="{{ route('profile.view') }}"
       class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/70 rounded-lg transition">
        <i data-lucide="user-circle" class="w-5 h-5"></i>
        <span>プロフィール</span>
    </a>

    <a href="{{ route('groups.index') }}"
       class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/70 rounded-lg transition">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span>グループ一覧</span>
    </a>

    {{-- 👥 メンバー管理（←これが今回の追加） --}}
    @if ($currentGroupId)
        <a href="{{ route('group.members.index', ['group' => $currentGroupId]) }}"
           class="flex items-start gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-gray-700/70 rounded-lg transition">
            <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600 dark:text-indigo-300"></i>
            <div class="flex flex-col leading-tight">
                <span class="font-semibold text-[13px] text-indigo-600 dark:text-indigo-300">メンバー管理</span>
                <span class="text-[11px] text-gray-500 dark:text-gray-400">メンバー追加・削除</span>
            </div>
        </a>
    @else
        {{-- グループ未選択時のフォールバック（クリックしても何もしない見た目だけの表示） --}}
        <div class="px-4 py-2 text-[11px] text-gray-400 dark:text-gray-500">
            グループ未選択のため<br>メンバー管理は表示できません
        </div>
    @endif

    {{-- 🚪 ログアウト --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-gray-200 dark:border-gray-700 pt-2">
        @csrf
        <button type="submit"
                class="w-full text-left flex items-center gap-2 px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-800/40 rounded-lg transition">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span>ログアウト</span>
        </button>
    </form>
</div>

            </div>
        </div>
    </div>
</nav>



{{-- 🧭 ページヘッダー --}}
@isset($header)
    <header class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
@endisset

{{-- 📄 メインコンテンツ --}}
<main class="flex-1 bg-[#fdf4f4ff] dark:bg-gray-900 transition-colors duration-300">
    {{ $slot }}
</main>

{{-- 📌 フッター --}}
<footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
    © {{ date('Y') }} MilLog. 
</footer>

</div>

{{-- ✅ 各ページ固有スクリプト --}}
@stack('scripts')
</body>
</html>
