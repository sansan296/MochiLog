<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('メモ詳細') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <a href="{{ route('items.show', $item) }}" class="text-blue-500 hover:text-blue-700 mr-2">在庫表示に戻る</a>
          <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $item->item }}: {{ $item->user->name }}</p>
          <p class="text-gray-800 dark:text-gray-300 text-lg">{{ $memo->memo }}</p>
          <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $memo->user->name }}</p>
          <div class="text-gray-6000 dark:text-gray-400 text-sm">
            <p>メモ追加日時: {{ $memo->created_at->format('Y-m-d H:i') }}</p>
            <p>メモ最終更新: {{ $memo->updated_at->format('Y-m-d H:i') }}</p>
          </div>
          @if (auth()->id() === $memo->user_id)
          <div class="flex mt-4">
            <a href="{{ route('items.memos.edit', [$item, $memo]) }}" class="text-blue-500 hover:text-blue-700 mr-2">編集</a>
            <form action="{{ route('items.memos.destroy', [$item, $memo]) }}" method="POST" onsubmit="return confirm('削除しますか？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-500 hover:text-red-700">削除</button>
            </form>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>