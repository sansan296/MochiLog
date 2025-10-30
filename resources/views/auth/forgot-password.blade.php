<x-guest-layout>
  {{-- 💡 Jetstream デフォルト背景を打ち消す --}}
  <style>
    body > div.min-h-screen {
      background: none !important;
    }
  </style>

  {{-- 🌈 全画面グラデーション背景 --}}
  <div class="min-h-screen w-full flex flex-col justify-center items-center 
              bg-gradient-to-br from-indigo-100 via-white to-pink-100 
              dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 
              bg-cover bg-center transition-all duration-700">

    {{-- 🏷️ タイトル --}}
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
        パスワードをお忘れですか？
      </h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">
        ご登録のメールアドレスを入力してください。<br>
        パスワード再設定用のリンクをお送りします。
      </p>
    </div>

    {{-- 💡 入力フォームカード --}}
    <div class="w-[90%] sm:w-full max-w-md bg-white dark:bg-gray-800 
                shadow-2xl rounded-2xl p-8 border border-gray-100 dark:border-gray-700 
                transition-all duration-300 hover:scale-[1.01]">

      {{-- セッションステータス --}}
      <x-auth-session-status class="mb-4" :status="session('status')" />

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- メールアドレス -->
        <div>
          <x-input-label for="email" :value="__('メールアドレス')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="email"
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400"
                        type="email" name="email" :value="old('email')" required autofocus />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- ボタンエリア -->
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
          <a href="{{ route('login') }}"
             class="w-full sm:w-auto text-center text-sm px-4 py-2 rounded-lg 
                    bg-gray-300 hover:bg-gray-400 text-gray-800 
                    dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 
                    transition-all duration-300 transform hover:scale-[1.03]">
            ← ログイン画面へ戻る
          </a>

          <x-primary-button 
              class="w-full sm:w-[70px] text-sm px-4 py-2 bg-indigo-500 hover:bg-indigo-600 
                     dark:bg-indigo-600 dark:hover:bg-indigo-500 
                     rounded-lg transition-all duration-300 transform hover:scale-[1.03]">
            {{ __('送信') }}
          </x-primary-button>
        </div>
      </form>
    </div>

    {{-- フッター --}}
    <footer class="mt-10 text-gray-400 text-xs text-center">
      ©MiLog.
    </footer>
  </div>
</x-guest-layout>
