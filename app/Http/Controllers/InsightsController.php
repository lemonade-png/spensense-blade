<?php

namespace App\Http\Controllers;

use App\Models\User;
use Brick\Math\BigDecimal;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class InsightsController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $month = today()->startOfMonth();

        return view('dashboard', [
            'totals' => $this->totals($user),
            ...$this->charts($user, $month),
            'period' => $month->format('Y-m'),
            'recent' => $user->transactions()->with('category:id,name')
                ->orderByDesc('transaction_date')->orderByDesc('id')->limit(5)->get(),
        ]);
    }

    public function reports(Request $request): View
    {
        $data = $request->validate(['period' => ['nullable', 'date_format:Y-m']]);
        $period = $data['period'] ?? today()->format('Y-m');
        $month = today()->setDate((int) substr($period, 0, 4), (int) substr($period, 5, 2), 1);
        $user = $request->user();

        return view('reports', [
            'period' => $period,
            'totals' => $this->totals($user, $month),
            ...$this->charts($user, $month),
        ]);
    }

    /** @return array{income: string, expenses: string, balance: string, count: int} */
    private function totals(User $user, ?CarbonInterface $month = null): array
    {
        $query = $user->transactions();
        if ($month !== null) {
            $query->whereBetween('transaction_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()]);
        }

        $totals = $query->selectRaw('type, ROUND(SUM(amount), 2) AS total, COUNT(*) AS entries')->groupBy('type')->get()->keyBy('type');
        $income = $this->decimal($totals->get('income')->total ?? 0);
        $expense = $this->decimal($totals->get('expense')->total ?? 0);

        return [
            'income' => (string) $income,
            'expenses' => (string) $expense,
            'balance' => (string) $income->minus($expense),
            'count' => (int) $totals->sum('entries'),
        ];
    }

    /** @return array{history: list<array{period: string, income: float, expenses: float}>, breakdown: Collection<int, array{name: string, amount: float}>} */
    private function charts(User $user, CarbonInterface $month): array
    {
        $start = $month->copy()->subMonths(5);
        $end = $month->copy()->endOfMonth();
        $rows = $user->transactions()->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('SUBSTR(transaction_date, 1, 7) AS period, type, ROUND(SUM(amount), 2) AS total')
            ->groupByRaw('SUBSTR(transaction_date, 1, 7), type')->get()->groupBy('period');

        $history = [];
        for ($offset = 0; $offset < 6; $offset++) {
            $date = $start->copy()->addMonths($offset);
            $totals = $rows->get($date->format('Y-m'))?->keyBy('type');
            $history[] = [
                'period' => $date->format('M Y'),
                'income' => (float) ($totals?->get('income')->total ?? 0),
                'expenses' => (float) ($totals?->get('expense')->total ?? 0),
            ];
        }

        $breakdown = $user->transactions()->with('category:id,name')->where('type', 'expense')
            ->whereBetween('transaction_date', [$month->toDateString(), $end->toDateString()])
            ->selectRaw('category_id, ROUND(SUM(amount), 2) AS total')
            ->groupBy('category_id')->orderByDesc('total')->orderBy('category_id')->get()
            ->map(fn ($row) => ['name' => $row->category->name, 'amount' => (float) $row->total]);

        return compact('history', 'breakdown');
    }

    private function decimal(int|float|string $amount): BigDecimal
    {
        return BigDecimal::of((string) $amount)->toScale(2);
    }
}
