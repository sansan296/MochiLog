<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          グループ一覧
      </h2>
  </x-slot>

  <div class="max-w-4xl mx-auto p-6">
      <a href="{{ route('groups.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
          ➕ 新規グループ作成
      </a>

      <ul class="mt-6 space-y-4">
          @foreach($groups as $group)
              <li class="bg-white shadow rounded-md p-4 flex justify-between items-center">
                  <div>
                      <h3 class="font-bold text-lg">{{ $group->name }}</h3>
                      <p class="text-gray-600">モード：{{ $group->mode === 'household' ? '家庭用' : '企業用' }}</p>
                  </div>
                  <div class="flex space-x-3">
                      <a href="{{ route('groups.edit', $group) }}" class="text-blue-500 hover:underline">編集</a>
                      <form method="POST" action="{{ route('groups.destroy', $group) }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="text-red-500 hover:underline"
                              onclick="return confirm('本当に削除しますか？')">削除</button>
                      </form>
                  </div>
              </li>
          @endforeach
      </ul>
  </div>
</x-app-layout>
