<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          グループ名の編集
      </h2>
  </x-slot>

  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
      <form method="POST" action="{{ route('groups.update', $group) }}">
          @csrf
          @method('PUT')

          <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">グループ名</label>
              <input type="text" name="name" value="{{ $group->name }}" class="w-full border rounded-md p-2" required>
          </div>

          <div class="flex justify-end">
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                  更新
              </button>
          </div>
      </form>
  </div>
</x-app-layout>
