<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white text-center">
      メンバーを追加
    </h2>
  </x-slot>

  <div class="max-w-md mx-auto py-8 px-4">

    {{-- ✅ 成功・エラーメッセージ --}}
    @if (session('success'))
      <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded-lg shadow">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 bg-red-100 text-red-800 px-4 py-2 rounded-lg shadow">{{ session('error') }}</div>
    @endif
    @if (session('info'))
      <div class="mb-4 bg-blue-100 text-blue-800 px-4 py-2 rounded-lg shadow">{{ session('info') }}</div>
    @endif

    {{-- 📋 メンバー追加フォーム --}}
    <form method="POST" action="{{ route('group.members.store', ['group' => $group->id]) }}"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
      @csrf

      {{-- メールアドレス入力欄 --}}
      <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
          招待するユーザーのメールアドレス
        </label>
        <input type="email"
               name="email"
               id="email"
               required
               class="w-full border rounded-lg px-3 py-2 text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring focus:ring-indigo-200 focus:border-indigo-400"
               placeholder="example@example.com">
        @error('email')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- 送信ボタン --}}
      <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
          <i data-lucide="send" class="w-4 h-4"></i>
          送信
        </button>
      </div>
    </form>

    {{-- 🔙 戻るボタン --}}
    <div class="mt-6 text-center">
      <a href="{{ route('group.members.index', ['group' => $group->id]) }}"
         class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
        ← メンバー一覧に戻る
      </a>
    </div>
  </div>
</x-app-layout>
