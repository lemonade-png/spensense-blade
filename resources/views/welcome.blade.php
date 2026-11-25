@extends('layout')
@section('title', 'Personal expense tracker')
@section('main-class', 'welcome-main')
@section('content')
    <section class="panel welcome-panel">
        <h1>A clearer view of your money</h1>
        <p>Track income and expenses in PKR, set a monthly budget, and see where your money goes.</p>
        <div class="actions">
            @auth
                <a class="button" href="{{ route('dashboard') }}">Open dashboard</a>
            @else
                <a class="button" href="{{ route('register') }}">Create account</a>
                <a class="button secondary" href="{{ route('login') }}">Log in</a>
            @endauth
        </div>
    </section>
@endsection
