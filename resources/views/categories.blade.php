@extends('layout')
@section('title', 'Categories')
@section('content')
    <h1>Categories</h1>
    <div class="columns">
        <section class="panel">
            <h2>Your categories</h2>
            @forelse ($categories as $category)
                <details>
                    <summary>{{ $category->name }} · {{ ucfirst($category->type) }}</summary>
                    <form method="post" action="{{ route('categories.update', $category) }}" class="grid">
                        @csrf @method('PUT')
                        <label>Name <input name="name" value="{{ $category->name }}" maxlength="80" required></label>
                        <label>Type <select name="type">
                            <option value="expense" @selected($category->type === 'expense')>Expense</option>
                            <option value="income" @selected($category->type === 'income')>Income</option>
                        </select></label>
                        <button>Save changes</button>
                    </form>
                    @if ($category->transactions_count || $category->budgets_count)
                        <small>In use by {{ $category->transactions_count }} transactions and {{ $category->budgets_count }} budgets.</small>
                    @else
                        <form method="post" action="{{ route('categories.destroy', $category) }}" data-confirm="Delete this category?">
                            @csrf @method('DELETE') <button class="secondary">Delete</button>
                        </form>
                    @endif
                </details>
            @empty
                <p>No categories yet.</p>
            @endforelse
        </section>
        <form method="post" action="{{ route('categories.store') }}" class="panel grid">
            @csrf
            <h2>New category</h2>
            <label>Name <input name="name" maxlength="80" required></label>
            <label>Type <select name="type"><option value="expense">Expense</option><option value="income">Income</option></select></label>
            <button>Add category</button>
        </form>
    </div>
@endsection
