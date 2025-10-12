<x-app-layout> 
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('作れる料理一覧') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4">
        @isset($message)
            <p class="text-center text-gray-600 dark:text-gray-400 mb-4">{{ $message }}</p>
        @endisset

        @if(!empty($recipes))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recipes as $recipe)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 text-center">
                                {{ $recipe['title'] }}
                            </h3>
                            <img src="{{ $recipe['image'] }}" alt="{{ $recipe['title'] }}"
                                 class="w-full rounded mb-3">

                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                使用食材: 
                                {{ collect($recipe['usedIngredients'])->pluck('name')->implode(', ') }}
                            </p>

                            @if(!empty($recipe['missedIngredients']))
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                                    足りない食材: 
                                    {{ collect($recipe['missedIngredients'])->pluck('name')->implode(', ') }}
                                </p>
                            @endif
                        </div>

                        <div class="text-center mt-4">
                            <a href="https://spoonacular.com/recipes/{{ Str::slug($recipe['title']) }}-{{ $recipe['id'] }}"
                               target="_blank"
                               class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                               詳細を見る
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400">作れる料理は見つかりませんでした。</p>
        @endif
    </div>
</x-app-layout>
