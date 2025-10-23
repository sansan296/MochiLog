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

    <title>{{ config('app.name', 'もちログ') }}</title>
    <link rel="icon" href="{{ asset('images/MochiLog-icon.svg') }}" type="image/svg+xml">

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

    <!-- 💡 ダークモード保持 -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('darkMode', this.dark);
                },
                dark: localStorage.getItem('darkMode') === 'true'
            });
        });
    </script>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-screen flex flex-col">

        {{-- 🌐 ナビゲーションバー：フェードスライド版 --}}
<nav 
    x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-50"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">

        {{-- ⏰ 左：現在時刻 --}}
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
             x-text="time"
        ></div>

        {{-- 🌟 右側ボタン群 --}}
        <div class="flex items-center space-x-3 sm:space-x-4 relative">

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

            {{-- 🍔 ハンバーガーメニュー（フェード＋スライド） --}}
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open" 
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                    title="メニュー"
                >
                    <i data-lucide="menu" class="w-6 h-6 text-gray-700 dark:text-gray-200"></i>
                </button>

                {{-- ドロップダウン（ガラス風） --}}
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
                    @foreach ([
                        ['route' => 'menu.index', 'icon' => 'grid', 'label' => 'メニュー'],
                        ['route' => 'settings.index', 'icon' => 'settings', 'label' => '設定'],
                        ['route' => 'profile.view', 'icon' => 'user-circle', 'label' => 'プロフィール'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/70 rounded-lg transition">
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
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
            © {{ date('Y') }} もちログ. All rights reserved.
        </footer>
    </div>

    {{-- ✅ 各ページ固有スクリプト --}}
    @stack('scripts')
</body>
</html>
