<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          メンバー一覧
      </h2>
  </x-slot>

  <div class="max-w-3xl mx-auto py-8 px-4">

    {{-- ✅ フラッシュメッセージ --}}
    @if (session('success'))
      <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded-lg shadow">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 bg-red-100 text-red-800 px-4 py-2 rounded-lg shadow">{{ session('error') }}</div>
    @endif
    @if (session('info'))
      <div class="mb-4 bg-blue-100 text-blue-800 px-4 py-2 rounded-lg shadow">{{ session('info') }}</div>
    @endif

    {{-- ➕ メンバー追加ボタン --}}
    <div class="flex justify-end mb-4">
      <a href="{{ route('group.members.create', ['group' => $group->id]) }}"
         class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>メンバーを追加</span>
      </a>
    </div>

    {{-- 👥 メンバー一覧 --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow divide-y divide-gray-200 dark:divide-gray-700">
      @forelse ($members as $member)
        <div class="flex items-center justify-between p-4 text-sm text-gray-700 dark:text-gray-100">
          <div>
            <p class="font-semibold">{{ $member->user->name ?? '（名前未登録）' }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $member->user->email ?? '不明なユーザー' }}</p>
          </div>

          {{-- 🗑️ 削除ボタン（自分以外のみ） --}}
          @if ($member->user && auth()->id() !== $member->user->id)
            <div x-data="{ open: false }">
              <!-- 削除ボタン -->
              <button 
                @click="open = true"
                class="text-red-500 text-xs hover:text-red-600 font-medium flex items-center gap-1 transition">
                <i data-lucide="trash-2" class="w-4 h-4"></i> 削除
              </button>

              <!-- 🧩 カスタム削除確認モーダル -->
              <div 
                x-show="open"
                x-cloak
                class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div 
                  @click.outside="open = false"
                  x-transition
                  class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-80 text-center space-y-4 transform transition-all duration-300">
                  
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">確認</h3>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">本当にこのメンバーを削除しますか？</p>

                  <div class="flex justify-center gap-3 pt-2">
                    <button 
                      @click="open = false"
                      class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition">
                      キャンセル
                    </button>

                    <form method="POST"
                          action="{{ route('group.members.destroy', ['group' => $group->id, 'user' => $member->user->id]) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-md transition">
                        削除
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
      @empty
        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
          現在、このグループにはメンバーがいません。
        </div>
      @endforelse
    </div>
  </div>

  {{-- ✅ Alpine.js が読み込まれていない場合は必ず追加 --}}
  <script src="https://unpkg.com/alpinejs" defer></script>
</x-app-layout>
