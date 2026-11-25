@extends('layout')
@section('title', 'Transactions')
@section('content')
    <header class="heading"><h1>Transactions</h1><a class="button" href="{{ route('transactions.create') }}">Add transaction</a></header>
    <form class="panel filters" method="get">
        <label>Search by title <input name="search" value="{{ $filters['search'] ?? '' }}" maxlength="150"></label>
        <label>Type <select name="type">
            <option value="">All types</option>
            @foreach (['income' => 'Income', 'expense' => 'Expense'] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select></label>
        <label>Category <select name="category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select></label>
        <button>Apply filters</button>
    </form>
    <section class="panel">
        @include('partials.transactions', ['rows' => $transactions, 'editable' => true])
        @if ($transactions->hasPages())
            <nav class="heading" aria-label="Pagination">
                <span>Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</span>
                <div class="actions">
                    @if ($url = $transactions->previousPageUrl()) <a href="{{ $url }}">Previous</a> @endif
                    @if ($url = $transactions->nextPageUrl()) <a href="{{ $url }}">Next</a> @endif
                </div>
            </nav>
        @endif
    </section>
@endsection
