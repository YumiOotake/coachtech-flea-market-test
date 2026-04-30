@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/auth-common.css') }}">
@endsection
@section('content')
    <div class="form__content">
        <div class="form__notice">
            <p class="form__notice-text">
                登録したメールアドレスに認証リンクを送信しました。<br>
                メール認証を完了してください。
            </p>
        </div>
        <div class="form__button">
            <a href="https://mailtrap.io" class="form__button-link">認証はこちらから</a>
        </div>
        <form method="POST" action="{{ route('verification.send') }}" class="form">
            @csrf
            <div class="form__button">
                <button type="submit" class="form__button-submit">認証メールを再送する</button>
            </div>
        </form>
    </div>
@endsection
