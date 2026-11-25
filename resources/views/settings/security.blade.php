@extends('layout')
@section('title', 'Change password')
@section('content')
    <form class="panel narrow grid" method="post" action="{{ route('user-password.update') }}">
        @csrf @method('PUT')
        <h1>Change password</h1>
        <label>Current password <input type="password" name="current_password" autocomplete="current-password" required></label>
        <label>New password <input type="password" name="password" autocomplete="new-password" required></label>
        <label>Confirm new password <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button>Save password</button>
    </form>
@endsection
