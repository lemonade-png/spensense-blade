<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title>@yield('title', 'SpendSense') · SpendSense</title>
    <link rel="stylesheet" href="{{ asset('spendsense.css') }}">
    <script src="{{ asset('spendsense.js') }}" defer></script>
</head>
<body class="{{ auth()->check() ? 'app-shell' : 'guest-shell' }}">
    <a class="skip-link" href="#content">Skip to content</a>
    <header class="top">
        <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('home') }}">
            <span class="brand-mark" aria-hidden="true"><i></i><i></i><i></i></span>
            SpendSense
        </a>
        @auth
            <nav class="main-nav" aria-label="Main navigation">
                @foreach ([
                    ['dashboard', 'dashboard', 'Overview', '◫'],
                    ['transactions.index', 'transactions.*', 'Transactions', '↔'],
                    ['categories.index', 'categories.*', 'Categories', '◇'],
                    ['budgets.index', 'budgets.*', 'Budgets', '▤'],
                    ['reports', 'reports', 'Reports', '▥'],
                    ['profile.edit', 'profile.*|security.*|appearance.*', 'Settings', '⚙'],
                ] as [$route, $pattern, $label, $icon])
                    <a href="{{ route($route) }}" @if (request()->routeIs(...explode('|', $pattern))) aria-current="page" @endif>
                        <span class="nav-icon" aria-hidden="true">{{ $icon }}</span>{{ $label }}
                    </a>
                @endforeach
            </nav>
            <div class="account">
                <a class="account-name" href="{{ route('profile.edit') }}" aria-label="Profile for {{ auth()->user()->name }}">
                    <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span>{{ auth()->user()->name }}</span>
                </a>
                <form method="post" action="{{ route('logout') }}">@csrf <button class="secondary">Log out</button></form>
            </div>
        @else
            <nav class="guest-nav" aria-label="Account">
                <a href="{{ route('login') }}">Log in</a>
                <a class="button secondary" href="{{ route('register') }}">Register</a>
            </nav>
        @endauth
    </header>
    <main id="content" class="@yield('main-class')">
        @if (session('success'))
            <p role="status" class="notice">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <div role="alert" class="errors">
                <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
