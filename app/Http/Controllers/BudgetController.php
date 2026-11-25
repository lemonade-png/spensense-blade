<?php

namespace App\Http\Controllers;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['period' => ['nullable', 'date_format:Y-m']]);
        $period = $filters['period'] ?? today()->format('Y-m');
        $start = today()->setDate((int) substr($period, 0, 4), (int) substr($period, 5, 2), 1);
        $user = $request->user();
        $spent = $user->transactions()->where('type', 'expense')
            ->whereBetween('transaction_date', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()])
            ->selectRaw('category_id, ROUND(SUM(amount), 2) AS total')
            ->groupBy('category_id')->pluck('total', 'category_id');

        $budgets = $user->budgets()->with('category:id,name')
            ->where('year', $start->year)->where('month', $start->month)
            ->orderBy('id')->get()->map(function ($budget) use ($spent) {
                $limit = (string) $budget->amount;
                $used = BigDecimal::of((string) ($spent[$budget->category_id] ?? 0))->toScale(2);

                return [
                    ...$budget->toArray(),
                    'spent' => (string) $used,
                    'remaining' => (string) BigDecimal::of($limit)->minus($used),
                    'percentage' => (float) (string) $used->multipliedBy(100)->dividedBy($limit, 1, RoundingMode::HalfUp),
                ];
            });

        return view('budgets', [
            'budgets' => $budgets,
            'period' => $period,
            'categories' => $user->categories()->where('type', 'expense')->orderBy('name')->get(['id', 'name', 'type']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $budget = $request->user()->budgets()->create($this->validated($request));

        return to_route('budgets.index', ['period' => sprintf('%04d-%02d', $budget->year, $budget->month)]);
    }

    public function update(Request $request, string $budget): RedirectResponse
    {
        $record = $request->user()->budgets()->findOrFail($budget);
        $record->update($this->validated($request, $record->id));

        return to_route('budgets.index', ['period' => sprintf('%04d-%02d', $record->year, $record->month)]);
    }

    public function destroy(Request $request, string $budget): RedirectResponse
    {
        $record = $request->user()->budgets()->findOrFail($budget);
        $period = sprintf('%04d-%02d', $record->year, $record->month);
        $record->delete();

        return to_route('budgets.index', ['period' => $period]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?int $budget = null): array
    {
        $period = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:1900,9999'],
        ]);

        return [
            ...$period,
            ...$request->validate([
                'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
                'category_id' => ['required', 'integer',
                    Rule::exists('categories', 'id')->where('user_id', $request->user()->id)->where('type', 'expense'),
                    Rule::unique('budgets', 'category_id')->where('user_id', $request->user()->id)
                        ->where('year', $period['year'])->where('month', $period['month'])->ignore($budget),
                ],
            ], ['category_id.unique' => 'This category already has a budget for the selected month.']),
        ];
    }
}
