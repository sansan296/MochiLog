<x-guest-layout>
  <script src="https://unpkg.com/alpinejs" defer></script>

  {{-- ✅ 画面全体に背景を広げるため、固定レイヤーで定義 --}}
  <div class="fixed inset-0 bg-gradient-to-b from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800"></div>

  {{-- ✅ フォーム全体を中央に配置 --}}
  <div class="relative z-10 min-h-screen flex flex-col items-center justify-center">

      <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg w-full max-w-md text-center">

          <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">
              管理者パスワードを入力してください
          </h2>

          {{-- 🌟 初回パスワード自動登録メッセージ（手動で閉じる） --}}
          @if (session('first_time_info'))
              <div 
                  x-data="{ show: true }" 
                  x-show="show" 
                  x-transition.duration.300ms
                  class="relative bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 mb-6 rounded-xl shadow-sm"
              >
                  <button 
                      @click="show = false"
                      class="absolute top-2 right-3 text-yellow-700 hover:text-yellow-900 text-xl font-bold leading-none focus:outline-none"
                      title="閉じる"
                  >
                      ×
                  </button>
                  <div class="text-left font-medium">
                      {{ session('first_time_info') }}
                  </div>
              </div>
          @endif

          {{-- エラーメッセージ --}}
          @if ($errors->any())
              <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-xl">
                  {{ $errors->first('password') }}
              </div>
          @endif

          {{-- 入力フォーム --}}
          <form method="POST" action="{{ route('admin.password.gate.check') }}">
              @csrf
              <div class="mb-6 text-left">
                  <label for="password" class="block text-gray-700 dark:text-gray-200 font-medium mb-2">
                      共通管理者パスワード
                  </label>
                  <input type="password" name="password" id="password" required
                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400 focus:border-indigo-400">
                  @error('password')
                      <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                  @enderror
              </div>

              <div class="flex justify-end">
                <x-primary-button 
                class="w-auto sm:w-1/2 md:w-2/3 lg:w-full px-4 py-2 text-sm sm:text-base">
                    確認
            </x-primary-button>
        </div>

          </form>

      </div>
  </div>
</x-guest-layout>
