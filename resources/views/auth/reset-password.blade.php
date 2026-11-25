@extends('layout')
@section('title', 'Choose a new password')
@section('content')
    <form class="panel narrow grid" method="post" action="{{ route('password.update') }}">
        @csrf
        <h1>Choose a new password</h1>
        <input type="hidden" name="token" value="{{ $token }}">
        <label>Email <input name="email" type="email" value="{{ old('email', $email) }}" required></label>
        <label>Password <input type="password" name="password" autocomplete="new-password" required></label>
        <label>Confirm password <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button>Reset password</button>
    </form>
@endsection
