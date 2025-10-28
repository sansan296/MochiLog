<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white text-center">
      メンバー管理
    </h2>
  </x-slot>

  <div class="max-w-3xl mx-auto py-8 px-4">

    {{-- ✅ 成功・エラー・情報メッセージ --}}
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
         class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow">
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

          {{-- 🗑️ 削除ボタン（自分以外） --}}
          @if (auth()->id() !== ($member->user->id ?? null))
            <form method="POST"
                  action="{{ route('group.members.destroy', ['group' => $group->id, 'user' => $member->user->id]) }}"
                  onsubmit="return confirm('本当に削除しますか？')">
              @csrf
              @method('DELETE')
              <button class="text-red-500 text-xs hover:text-red-600 font-medium flex items-center gap-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i> 削除
              </button>
            </form>
          @endif
        </div>
      @empty
        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
          現在、このグループにはメンバーがいません。
        </div>
      @endforelse
    </div>

  </div>
</x-app-layout>
