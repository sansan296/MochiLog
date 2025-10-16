<x-app-layout> 
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
            {{ __('作れる料理一覧') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4">
        {{-- メッセージがあれば表示 --}}
        @isset($message)
            <p class="text-center text-gray-600 mb-4">{{ $message }}</p>
        @endisset

        {{-- レシピが存在するか --}}
        @if(!empty($recipes) && is_iterable($recipes))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recipes as $recipe)
                    {{-- 🧩 型チェックで安全に --}}
                    @if(is_array($recipe) && isset($recipe['title']))
                        <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between hover:shadow-lg transition">
                            <div>
                                {{-- タイトル --}}
                                <h3 class="text-lg font-bold text-gray-800 mb-2 text-center">
                                    {{ $recipe['translated_title'] ?? $recipe['title'] }}
                                </h3>

                                {{-- 画像（無い場合はダミー） --}}
                                <img src="{{ $recipe['image'] ?? 'https://via.placeholder.com/400x300?text=No+Image' }}"
                                     alt="{{ $recipe['title'] }}"
                                     class="w-full h-52 object-cover rounded mb-3">

                                {{-- 使用食材 --}}
                                @if(!empty($recipe['usedIngredients']) && is_array($recipe['usedIngredients']))
                                    <p class="text-gray-700 text-sm">
                                        使用食材:
                                        {{ collect($recipe['usedIngredients'])->pluck('name')->implode(', ') }}
                                    </p>
                                @endif

                                {{-- 足りない食材 --}}
                                @if(!empty($recipe['missedIngredients']) && is_array($recipe['missedIngredients']))
                                    <p class="text-gray-500 text-sm mt-1">
                                        足りない食材:
                                        {{ collect($recipe['missedIngredients'])->pluck('name')->implode(', ') }}
                                    </p>
                                @endif
                            </div>

                            {{-- ⭐ ブックマークボタン --}}
                            <div class="mt-3 text-center">
                                @php
                                    // コントローラ側で渡されている配列
                                    $isBookmarked = in_array($recipe['id'], $bookmarkedRecipeIds ?? []);
                                @endphp

                                @if($isBookmarked)
                                    {{-- すでにブックマーク済み --}}
                                    <form method="POST" action="{{ route('bookmarks.destroy', ['id' => $recipe['id']]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                                            ✅ 登録済み（削除）
                                        </button>
                                    </form>
                                @else
                                    {{-- 新規ブックマーク --}}
                                    <form method="POST" action="{{ route('bookmarks.store') }}">
                                        @csrf
                                        <input type="hidden" name="recipe_id" value="{{ $recipe['id'] }}">
                                        <input type="hidden" name="title" value="{{ $recipe['title'] }}">
                                        <input type="hidden" name="image_url" value="{{ $recipe['image'] ?? '' }}">
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">
                                            ⭐ ブックマーク
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- 詳細ボタン --}}
                            <div class="text-center mt-4">
                                <a href="https://spoonacular.com/recipes/{{ Str::slug($recipe['title']) }}-{{ $recipe['id'] ?? '' }}"
                                   target="_blank"
                                   class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                   詳細を見る
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            {{-- データが空または不正 --}}
            <p class="text-center text-gray-500 mt-8">作れる料理は見つかりませんでした。</p>
        @endif
    </div>
</x-app-layout>
