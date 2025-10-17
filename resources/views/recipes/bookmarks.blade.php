<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
            {{ __('ブックマークしたレシピ一覧') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4">
        @if($bookmarks->isEmpty())
            <p class="text-center text-gray-500 mt-8">ブックマークしたレシピはありません。</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($bookmarks as $bookmark)
                    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between hover:shadow-lg transition">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2 text-center">
                                {{ $bookmark->title }}
                            </h3>
                            <img src="{{ $bookmark->image_url ?? 'https://via.placeholder.com/400x300?text=No+Image' }}"
                                 alt="{{ $bookmark->title }}"
                                 class="w-full h-52 object-cover rounded mb-3">
                        </div>
                        <div class="text-center mt-4">
                            <form method="POST" action="{{ route('bookmarks.destroy', ['id' => $bookmark->recipe_id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-block px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                    ブックマーク解除
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
