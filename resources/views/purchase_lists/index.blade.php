<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
      購入予定品
    </h2>
  </x-slot>

  <div class="py-8 max-w-4xl mx-auto px-4">

    <!-- 登録フォーム -->
    <form method="POST" action="{{ route('purchase_lists.store') }}" class="mb-6 bg-white p-4 rounded-lg shadow">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <input type="text" name="item" placeholder="商品名" required
               class="border rounded-lg px-3 py-2 w-full">
        <input type="number" name="quantity" placeholder="個数（任意）" min="1"
               class="border rounded-lg px-3 py-2 w-full">
        <input type="date" name="purchase_date"
               class="border rounded-lg px-3 py-2 w-full">
      </div>
      <div class="text-center">
        <button type="submit" class="px-6 py-2 bg-[#4973B5] text-white rounded-lg hover:bg-[#2C5BA5]">
          追加
        </button>
      </div>
    </form>

    <!-- リスト一覧 -->
    <div class="bg-white p-4 rounded-lg shadow">
      @if($lists->isEmpty())
        <p class="text-center text-gray-800">購入予定のものはありません。</p>
      @else
        <table class="w-full text-center">
          <thead>
            <tr class="border-b">
              <th>商品名</th>
              <th>個数</th>
              <th>購入予定日</th>
              <th>追加</th>
            </tr>
          </thead>
          <tbody>

        @foreach($lists as $list)
            <tr class="border-b">
                <td class="py-2">{{ $list->item }}</td>
                <td>{{ $list->quantity ?? '-' }}</td>
                <td>{{ $list->purchase_date ? \Carbon\Carbon::parse($list->purchase_date)->format('Y/m/d') : '-' }}</td>
                <td>
                <div class="flex justify-center space-x-2">
                    <!-- 在庫へ追加：URL にクエリで値を渡す -->
                    <a href="{{ route('items.create', [
                        'item' => $list->item,
                        'quantity' => $list->quantity,
                'purchase_date' => $list->purchase_date
            ]) }}"
                    class="px-3 py-1 bg-[#4973B5] text-white rounded hover:bg-[#2C5BA5]">
                    在庫へ追加
                    </a>

                    <!-- 削除 -->
                    <form method="POST" action="{{ route('purchase_lists.destroy', $list->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-[#EE2E48] text-white rounded hover:bg-[#D52B3F]">削除</button>
                    </form>
                </div>
                </td>
            </tr>
            @endforeach

          </tbody>
        </table>
      @endif
    </div>
  </div>
</x-app-layout>
