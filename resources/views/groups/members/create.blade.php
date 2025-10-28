<x-app-layout>
  <x-slot name="header">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white text-center">メンバーを追加</h2>
  </x-slot>

  <div class="max-w-2xl mx-auto mt-10 bg-white dark:bg-gray-800 shadow rounded-2xl p-8">
      <p class="mb-6 text-gray-700 dark:text-gray-300 text-center">
          以下のボタンを押して、LINEでグループ招待リンクを共有してください。
      </p>

      <div class="text-center">
          <button 
              type="button"
              onclick="window.open('https://line.me/R/share?text={{ urlencode($joinUrl) }}', '_blank')"
              class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition-all duration-200">
              📱 LINEでリンクを共有
          </button>


  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">{{ $group->name }}</h3>

      <form method="POST" action="{{ route('group.members.store', $group) }}">
          @csrf

          <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">招待するユーザーのメールアドレス</label>
              <input type="email" name="email" class="w-full border rounded-md p-2" placeholder="user@example.com" required>
          </div>

          <div class="flex justify-end">
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                  追加
              </button>
          </div>
      </form>

      <a href="{{ route('group.members.index', $group) }}" class="block text-center text-gray-600 hover:underline mt-4">
          ← 戻る
      </a>
  </div>
</x-app-layout>
