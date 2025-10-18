<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-100 leading-tight text-center">
      ⚙️ {{ __('設定') }}
    </h2>
  </x-slot>

  <script src="https://unpkg.com/alpinejs" defer></script>

  <div class="max-w-5xl mx-auto py-10 px-6 space-y-10">

    {{-- ✅ 成功メッセージ --}}
    @if (session('success'))
      <div class="bg-green-100 text-green-800 px-4 py-3 rounded-xl shadow text-center">
        {{ session('success') }}
      </div>
    @endif

    {{-- 🌙 テーマ設定 --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 transition-all duration-300">
      <h3 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i data-lucide="moon" class="w-6 h-6 text-indigo-500"></i>
        テーマ設定
      </h3>

      <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" class="flex items-center justify-between">
        <p class="text-gray-600 dark:text-gray-300 text-lg">ダークモードを切り替えます。</p>
        <button 
          @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); location.reload();" 
          class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-white font-medium transition-all duration-200"
          :class="darkMode ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-700 hover:bg-gray-800'">
          <template x-if="darkMode">
            <span><i data-lucide='sun' class='inline w-5 h-5'></i> ライトモード</span>
          </template>
          <template x-if="!darkMode">
            <span><i data-lucide='moon' class='inline w-5 h-5'></i> ダークモード</span>
          </template>
        </button>
      </div>
    </div>

    {{-- 🔔 通知設定 --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 transition-all duration-300">
      <h3 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i data-lucide="bell" class="w-6 h-6 text-indigo-500"></i>
        通知設定
      </h3>

      <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
          <span class="text-gray-700 dark:text-gray-200">在庫が少なくなったら通知</span>
          <input type="checkbox" name="notify_low_stock" class="w-5 h-5 accent-indigo-500" 
            {{ $user->notify_low_stock ? 'checked' : '' }}>
        </label>


        <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
          <span class="text-gray-700 dark:text-gray-200">システムメンテナンス情報</span>
          <input type="checkbox" name="notify_system" class="w-5 h-5 accent-indigo-500"
            {{ $user->notify_system ? 'checked' : '' }}>
        </label>

        <div class="text-right pt-4">
          <button type="submit" class="px-5 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition">
            保存
          </button>
        </div>
      </form>
    </div>

    {{-- 🏠 モード設定 --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 transition-all duration-300">
      <h3 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i data-lucide="home" class="w-6 h-6 text-indigo-500"></i>
        モード設定
      </h3>

      <p class="text-gray-600 dark:text-gray-300 mb-4">家庭モード / 企業モードを切り替えます。</p>

      <div class="flex gap-4">
        <a href="{{ route('dashboard.home') }}" 
           class="flex-1 text-center py-3 rounded-xl bg-blue-500 text-white font-semibold hover:bg-blue-600 transition">
           家庭モード
        </a>
        <a href="{{ route('dashboard.company') }}" 
           class="flex-1 text-center py-3 rounded-xl bg-green-500 text-white font-semibold hover:bg-green-600 transition">
           企業モード
        </a>
      </div>
    </div>

    {{-- 🧾 アカウント情報 --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 transition-all duration-300">
      <h3 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i data-lucide="user" class="w-6 h-6 text-indigo-500"></i>
        アカウント情報
      </h3>

      <div class="flex flex-col sm:flex-row justify-between sm:items-center">
        <div>
          <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold">{{ Auth::user()->name }}</p>
          <p class="text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
        </div>
        <a href="{{ route('profile.edit') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center gap-2 bg-gray-700 dark:bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-gray-800 dark:hover:bg-indigo-700 transition">
          <i data-lucide="edit-3" class="w-5 h-5"></i> プロフィールを編集
        </a>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => lucide.createIcons());
  </script>
</x-app-layout>
