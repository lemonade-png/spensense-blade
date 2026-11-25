@extends('layout')
@section('title', isset($transaction) ? 'Edit transaction' : 'Add transaction')
@section('content')
    <h1>@yield('title')</h1>
    <form class="panel narrow grid" method="post" data-transaction-form
          action="{{ isset($transaction) ? route('transactions.update', $transaction) : route('transactions.store') }}">
        @csrf
        @isset($transaction) @method('PUT') @endisset
        <label>Title <input name="title" maxlength="150" required value="{{ old('title', $transaction->title ?? '') }}"></label>
        <div class="pair">
            <fieldset class="choice">
                <legend>Type</legend>
                @foreach (['expense' => 'Expense', 'income' => 'Income'] as $value => $label)
                    <label><input type="radio" name="type" value="{{ $value }}" @checked(old('type', $transaction->type ?? 'expense') === $value)><span>{{ $label }}</span></label>
                @endforeach
            </fieldset>
            <label>Category
                <select name="category_id" required>
                    <option value="">Select a category</option>
                    @foreach ($categories->groupBy('type') as $type => $items)
                        <optgroup label="{{ ucfirst($type) }}" data-type="{{ $type }}">
                            @foreach ($items as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $transaction->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @if ($categories->isEmpty()) <a href="{{ route('categories.index') }}">Create a category</a> @endif
            </label>
        </div>
        <div class="pair">
            <label>Amount (PKR) <input name="amount" type="number" min="0.01" max="9999999999.99" step="0.01" required value="{{ old('amount', $transaction->amount ?? '') }}"></label>
            <label>Date <input name="transaction_date" type="date" required value="{{ old('transaction_date', isset($transaction) ? $transaction->transaction_date->toDateString() : $today) }}"></label>
        </div>
        <label>Notes <textarea name="notes" rows="3" maxlength="2000">{{ old('notes', $transaction->notes ?? '') }}</textarea></label>
        <div class="actions"><button>{{ isset($transaction) ? 'Save changes' : 'Add transaction' }}</button><a class="button secondary" href="{{ route('transactions.index') }}">Cancel</a></div>
    </form>
@endsection
