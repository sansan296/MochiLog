<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
            {{ __('通知') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 期限切れ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4  text-[#EE2E48]">
                        ▼賞味期限切れの商品
                    </h3>
                    @if($expiredItems->isEmpty())
                        <p class="text-gray-500">期限切れの商品はありません。</p>
                    @else
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-white">
                                    <th class="px-4 py-2">商品名</th>
                                    <th class="px-4 py-2">賞味期限</th>
                                    <th class="px-4 py-2">個数</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expiredItems as $item)
                                    <tr class="border-b border-gray-400">
                                   
                                        <td class="px-4 py-2 text-center text-[#EE2E48]">
                                            {{ $item->item }}
                                        </td>

        
                                        <td class="px-4 py-2 text-center text-[#EE2E48]">
                                            {{ $item->expiration_date->format('Y/m/d') }}
                                        </td>

        
                                    <td class="px-4 py-2 text-center text-[#EE2E48]">
                                            {{ $item->quantity }}
                                    </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- 期限間近 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4  text-gray-600">
                        ▼期限が1週間以内の商品
                    </h3>
                    @if($nearExpiredItems->isEmpty())
                        <p class="text-center text-gray-500">期限間近の商品はありません。</p>
                    @else
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-white">
                                    <th class="px-4 py-2 text-center">商品名</th>
                                    <th class="px-4 py-2 text-center">賞味期限</th>
                                    <th class="px-4 py-2 text-center">個数</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nearExpiredItems as $item)
                                    <tr class="border-b border-gray-400">
                                        <td class="px-4 py-2 text-center">{{ $item->item }}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{ $item->expiration_date->format('Y/m/d') }}
                                        </td>
                                        <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        {{-- メモ一覧 --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3 text-gray-600">▼メモ一覧</h3>

            @if($memos->isEmpty())
                <p class="text-gray-500">メモは登録されていません。</p>
            @else
                <ul class="space-y-4">
                    @foreach($memos as $memo)
                        <li class="border-b border-gray-400 pb-2">
                            <p class="font-semibold text-gray-800">
                                商品名：{{ $memo->item->item }}
                            </p>
                            <p class="text-gray-600 text-sm">
                                登録者：{{ $memo->user->name }}
                            </p>
                            <p class="block text-center text-gray-800 mt-1">
                                メモ：{{ $memo->memo }}
                            </p>

                            {{-- 削除ボタン --}}
                            <form action="{{ route('items.memos.destroy', [$memo->item_id, $memo->id]) }}" 
                                  method="POST" 
                                  class="mt-2 text-right"
                                  onsubmit="return confirm('メモを削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-500 hover:text-red-600 text-sm">
                                    削除
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
            </div>

        </div>
    </div>
</x-app-layout>
