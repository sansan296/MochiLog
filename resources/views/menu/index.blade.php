{{-- resources/views/menu/index.blade.php --}}
<x-app-layout>
  {{-- Alpine.js 読み込み --}}
  <script src="https://unpkg.com/alpinejs" defer></script>

  <div class="flex h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">

    <!-- 🌈 サイドナビ -->
    <aside 
      x-data="{ open: false }"
      class="relative flex flex-col items-center w-20 hover:w-56 bg-white/70 dark:bg-gray-800 backdrop-blur-md transition-all duration-300 shadow-xl overflow-hidden"
    >
      <!-- ロゴ -->
      <div class="py-6 text-2xl font-bold text-brand-primary">
        🌤️
      </div>

      <!-- メニュー -->
      <nav class="flex-1 flex flex-col space-y-2 w-full px-2">
        @php
          $menus = [
            ['name' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard.home'],
            ['name' => '在庫一覧', 'icon' => 'boxes', 'route' => 'items.index'],
            ['name' => '購入リスト', 'icon' => 'shopping-cart', 'route' => 'purchase_lists.index'],
            ['name' => 'レシピ', 'icon' => 'utensils', 'route' => 'recipes.index'],
            ['name' => 'ブックマーク', 'icon' => 'bookmark', 'route' => 'bookmarks.index'],
            ['name' => 'タグ管理', 'icon' => 'tag', 'route' => 'tags.index'],
            ['name' => '監査ログ', 'icon' => 'clipboard-list', 'route' => 'audit-logs.index'],
            ['name' => 'プロフィール', 'icon' => 'user', 'route' => 'profile.edit'],
          ];
        @endphp

        @foreach ($menus as $menu)
          <a href="{{ route($menu['route']) }}"
            class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-200 
                   hover:bg-blue-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200">
            <i class="lucide lucide-{{ $menu['icon'] }} w-5 h-5"></i>
            <span class="whitespace-nowrap" x-show="open" x-transition>{{ $menu['name'] }}</span>
          </a>
        @endforeach
      </nav>

      <!-- ユーザーアイコン -->
      <div class="mb-6">
        <img src="https://via.placeholder.com/40" alt="User" class="rounded-full border-2 border-blue-400">
      </div>
    </aside>

    <!-- 📊 メインコンテンツ -->
    <main class="flex-1 p-8 overflow-y-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
          メニュー
        </h1>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <x-primary-button class="bg-brand-primary hover:bg-brand-secondary text-white">
            ログアウト
          </x-primary-button>
        </form>
      </div>

      <!-- グリッドカード -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('items.index') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">📦 在庫一覧</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">登録されている在庫を確認・編集できます。</p>
        </a>

        <a href="{{ route('purchase_lists.index') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">🛒 購入リスト</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">買う必要のあるアイテムを管理します。</p>
        </a>

        <a href="{{ route('recipes.index') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">🍳 作れるレシピ</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">在庫から作れる料理を提案します。</p>
        </a>

        <a href="{{ route('bookmarks.index') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">🔖 ブックマーク</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">保存したレシピを一覧表示します。</p>
        </a>

        <a href="{{ route('tags.index') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">🏷 タグ管理</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">アイテムにタグを追加して整理します。</p>
        </a>

        <a href="{{ route('audit-logs.index') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">📜 監査ログ</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">操作履歴を確認します。</p>
        </a>

        <a href="{{ route('profile.edit') }}" 
           class="bg-white/80 dark:bg-gray-800 rounded-2xl shadow-md p-6 hover:shadow-lg transition-all duration-300">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">👤 プロフィール</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">ユーザー情報を編集できます。</p>
        </a>
      </div>
    </main>
  </div>
</x-app-layout>
