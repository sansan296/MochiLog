@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>ログイン種別を選択してください</h2>

    <form method="POST" action="{{ route('user.type.select') }}">
        @csrf
        <button type="submit" name="user_type" value="family" class="btn btn-primary m-3">
            家庭用
        </button>
        <button type="submit" name="user_type" value="company" class="btn btn-secondary m-3">
            企業用
        </button>
    </form>
</div>
@endsection
