<label>Expense category <select name="category_id" required>
    <option value="">Select a category</option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected(($budget['category_id'] ?? null) === $category->id)>{{ $category->name }}</option>
    @endforeach
</select></label>
<label>Limit (PKR) <input name="amount" type="number" min="0.01" max="9999999999.99" step="0.01" required value="{{ $budget['amount'] ?? '' }}"></label>
<div class="pair">
    <label>Month <select name="month">
        @for ($month = 1; $month <= 12; $month++)
            <option value="{{ $month }}" @selected($month === ($budget['month'] ?? (int) substr($period, 5, 2)))>{{ date('F', mktime(0, 0, 0, $month, 1, 2000)) }}</option>
        @endfor
    </select></label>
    <label>Year <input name="year" type="number" min="1900" max="9999" required value="{{ $budget['year'] ?? substr($period, 0, 4) }}"></label>
</div>
