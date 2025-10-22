<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-white leading-tight">
       管理者設定ページ
    </h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      @if (session('success'))
          <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 px-4 py-3 rounded-xl shadow mb-6 text-center text-sm sm:text-base">
              {{ session('success') }}
          </div>
      @endif
      @if (session('error'))
          <div class="bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 px-4 py-3 rounded-xl shadow mb-6 text-center text-sm sm:text-base">
              {{ session('error') }}
          </div>
      @endif

      {{-- タイトル --}}
      <h3 class="text-lg sm:text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">ユーザー</h3>

      {{-- 📱 スマホ対応用のラッパー --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-x-auto border border-gray-100 dark:border-gray-700">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
              <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                      <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">名前</th>
                      <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">メール</th>
                      <th class="px-4 sm:px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">権限</th>
                      <th class="px-4 sm:px-6 py-3"></th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @foreach (\App\Models\User::all() as $user)
                      <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                          <td class="px-4 sm:px-6 py-3 text-gray-800 dark:text-gray-100 break-words">{{ $user->name }}</td>
                          <td class="px-4 sm:px-6 py-3 text-gray-600 dark:text-gray-300 break-words">{{ $user->email }}</td>
                          <td class="px-4 sm:px-6 py-3 text-center">
                              @if ($user->is_admin)
                                  <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-100 rounded-full">管理者</span>
                              @else
                                  <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-100 rounded-full">一般</span>
                              @endif
                          </td>
                          <td class="px-4 sm:px-6 py-3 text-right">
                              <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline-block">
                                  @csrf
                                  <button type="submit"
                                      class="w-full sm:w-auto px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-white text-xs sm:text-sm font-medium 
                                             {{ $user->is_admin ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600' }}
                                             transition-all duration-300">
                                      {{ $user->is_admin ? '一般ユーザーに設定' : '管理者に設定' }}
                                  </button>
                              </form>
                          </td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  </div>
</x-app-layout>
