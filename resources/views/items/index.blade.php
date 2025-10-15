{{-- resources/views/items/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
      {{ __('在庫一覧') }}
    </h2>
  </x-slot>

  <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
    
    {{-- 🔍 検索フォーム --}}
    <form method="GET" action="{{ route('items.index') }}" class="mb-6 flex justify-between items-center">
      <div>
        <input type="text" name="keyword" value="{{ request('keyword') }}"
              placeholder="商品名"
              class="border rounded-lg px-3 py-2 w-64">
        <button type="submit"
                class="ml-2 px-4 py-2 bg-[#4973B5] text-white rounded-lg hover:bg-[#2C5BA5]">
         検索
        </button>
      </div>

      {{-- 🍳 在庫で作れる料理を表示ボタン --}}
      <a href="{{ route('recipes.index') }}" 
         class="px-6 py-2 bg-[#FF9A3C] text-white font-semibold rounded-lg hover:bg-[#4973B5] transition">
         在庫で作れる料理を表示
      </a>
    </form>

    {{-- 検索結果表示 --}}
    @if(request('keyword'))
      <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg mb-4 text-blue-800">
          検索ワード：<span class="font-semibold">「{{ request('keyword') }}」</span>　
          <span class="font-semibold">{{ $items->total() }}</span> 件ヒット　
          在庫合計：<span class="font-semibold">{{ $totalQuantity }}</span> 個
      </div>
    @endif

    {{-- 📦 在庫一覧 --}}
    <div class="bg-[#9cbcf0ff] overflow-hidden shadow-sm sm:rounded-lg p-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($items as $item)
          <div class="p-4 bg-white rounded-lg shadow">
            
            {{-- 商品名 --}}
            <p class="text-lg font-semibold mb-2">{{ $item->item }}</p>

            {{-- 🏷️ タグバー（個別商品に紐づく） --}}
            <x-tag-bar :item-id="$item->id" />

            {{-- 賞味期限 --}}
            <p class="text-gray-800 text-base mt-2">
              賞味期限：
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

            {{-- 個数と登録者 --}}
            <p class="text-gray-800 text-base">個数：{{ $item->quantity }}</p>
            <p class="text-gray-600 text-sm mb-2">登録者：{{ $item->user->name }}</p>

            {{-- 詳細ページリンク --}}
            <a href="{{ route('items.show', $item) }}" 
               class="block text-right text-[#4973B5] hover:text-[#2C5BA5] font-medium mt-2">
              詳細 →
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ページネーション --}}
  <div class="mt-6">
    {{ $items->appends(request()->query())->links() }}
  </div>
</x-app-layout>

@stack('scripts')
