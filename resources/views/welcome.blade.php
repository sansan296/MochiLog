<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'IeLog') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#fdf4f4ff] text-[#1b1b18] flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-md rounded-2xl px-10 py-12 w-full max-w-5xl flex flex-col lg:flex-row items-center justify-center gap-12">

        {{-- 左側：ロゴ --}}
        <div class="flex justify-center items-center">
            <img 
                src="{{ asset('images/ielog-login.svg') }}" 
                alt="IeLog ロゴ" 
                class="w-64 h-64 object-contain rounded-2xl shadow-md"
            >
        </div>

        {{-- 右側：ボタン --}}
        @if (Route::has('login'))
            <div class="flex flex-col gap-6 w-full max-w-xs text-center">
                <a href="{{ route('login') }}"
                    class="block py-3 rounded-xl bg-[#8FB3E6] text-white font-semibold text-base tracking-widest shadow hover:bg-[#7CA4DA] transition duration-200">
                    ログイン
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="block py-3 rounded-xl bg-[#FFC48A] text-white font-semibold text-base tracking-widest shadow hover:bg-[#F0AF6C] transition duration-200">
                        アカウント登録
                    </a>
                @endif
            </div>
        @endif
    </div>

</body>
</html>
