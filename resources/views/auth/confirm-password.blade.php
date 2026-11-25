@extends('layout')
@section('title', 'Confirm password')
@section('content')
    <form class="panel narrow grid" method="post" action="{{ route('password.confirm.store') }}">
        @csrf
        <h1>Confirm password</h1>
        <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
        <button>Confirm password</button>
    </form>
@endsection
