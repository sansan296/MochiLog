<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
      {{ __('詳細') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-[#9cbcf0ff] overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="p-4 bg-white rounded-lg shadow">
          <p class="text-lg font-semibold">{{ $item->item }}</p>
            
            <p class="text-gray-800 text-base">
              賞味期限:　
              @if ($item->expiration_date)
                @if ($item->expiration_date->isPast())
                  <span class="text-[#EE2E48] font-bold">
                    {{ $item->expiration_date->format('Y/m/d') }}（期限切れ）
                  </span>
                @else
                    {{ $item->expiration_date->format('Y/m/d') }}
                    （あと {{ ceil(now()->floatDiffInRealDays($item->expiration_date)) }} 日）
                @endif

              @else
                なし
              @endif
            </p>

            <p class="text-gray-800 text-base">
              個数:　{{ $item->quantity }}
            </p>

            <p class="text-gray-600 text-sm">登録者: {{ $item->user->name }}</p>

        <div class="flex items-center justify-end mt-4 space-x-2">
            <a href="{{ route('items.edit', $item) }}" 
              class="text-[#4973B5] hover:text-[#2C5BA5]">編集</a>

            <form action="{{ route('items.destroy', $item) }}" method="POST" 
                  onsubmit="return confirm('削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-[#EE2E48] hover:text-[#D52B3F]">削除</button>
            </form>
        </div>

        </div>
      </div>

        <div class="mt-4">
            <a href="{{ route('items.memos.create', $item) }}" class="text-white mr-2">メモを追加</a>
          </div>
          
          <div class="mt-4">
            @foreach ($item->memos as $memo)
            <a href="{{ route('items.memos.show', [$item, $memo]) }}">
              <p>{{ $memo->memo }}　<span class="text-gray-600 dark:text-gray-400 text-sm">{{ $memo->user->name }}　{{ $memo->created_at->format('Y/m/d H:i') }}</span></p>
            </a>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>