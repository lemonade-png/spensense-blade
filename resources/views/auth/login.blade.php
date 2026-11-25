@extends('layout')
@section('title', 'Log in')
@section('content')
    <form class="panel narrow grid" method="post" action="{{ route('login.store') }}">
        @csrf
        <h1>Log in</h1>
        @if ($status) <p role="status">{{ $status }}</p> @endif
        <label>Email <input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required></label>
        <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
        <label class="inline"><input type="checkbox" name="remember"> Remember me</label>
        <button>Log in</button>
        @if ($canResetPassword) <a href="{{ route('password.request') }}">Forgot password?</a> @endif
    </form>
@endsection
