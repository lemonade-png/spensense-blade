<?php

namespace Database\Seeders;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class DatabaseSeeder extends Seeder
{
    public function run(CreateNewUser $register): void
    {
        if (! app()->environment('local')) {
            return;
        }

        DB::transaction(function () use ($register): void {
            $email = 'demo@spendsense.test';
            $demo = User::query()->where('email', $email)->lockForUpdate()->first();

            if ($demo === null) {
                $demo = $register->create([
                    'name' => 'SpendSense Demo',
                    'email' => $email,
                    'password' => 'SpendSenseDemo2026!',
                    'password_confirmation' => 'SpendSenseDemo2026!',
                ]);

                $demo->markEmailAsVerified();
            }

            if ($demo->name !== 'SpendSense Demo' || ! $demo->hasVerifiedEmail()) {
                $this->command?->warn('The demo email belongs to an existing account. No sample data was added.');

                return;
            }

            if ($demo->transactions()->exists() || $demo->budgets()->exists()) {
                $this->command?->info('The demo account already has records. No sample data was added.');

                return;
            }

            $this->addSampleRecords($demo);
            $this->command?->info('Sample transactions and budgets added to the demo account.');
        });
    }

    private function addSampleRecords(User $demo): void
    {
        $categories = [];
        foreach ([
            'income' => ['Salary', 'Freelancing'],
            'expense' => ['Food', 'Transport', 'Education', 'Entertainment', 'Bills'],
        ] as $type => $names) {
            foreach ($names as $name) {
                $categories[$name] = $demo->categories()->firstOrCreate(['type' => $type, 'name' => $name]);
            }
        }

        $today = today();
        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $month = $today->copy()->startOfMonth()->subMonths($monthsAgo);
            $lastDay = $monthsAgo === 0 ? $today->day : $month->daysInMonth;
            $entries = [
                ['Part-time salary', 'Salary', 35000, 1],
                ['Freelance design project', 'Freelancing', 7000 + 700 * $monthsAgo, 10],
                ['Groceries', 'Food', 5600 + 280 * $monthsAgo, 3],
                ['Campus lunches', 'Food', 2600 + 150 * $monthsAgo, 12],
                ['Bus and ride fares', 'Transport', 2300 + 200 * $monthsAgo, 6],
                ['Internet bill', 'Bills', 3200, 4],
                ['Course materials', 'Education', 1500 + 400 * $monthsAgo, 15],
                ['Movie night', 'Entertainment', 1200 + 100 * $monthsAgo, 20],
            ];

            foreach ($entries as [$title, $category, $amount, $day]) {
                $entryCategory = $categories[$category] ?? throw new LogicException("Unknown demo category: {$category}");

                $demo->transactions()->create([
                    'category_id' => $entryCategory->id,
                    'title' => $title,
                    'type' => $entryCategory->type,
                    'amount' => $amount,
                    'transaction_date' => $month->copy()->day(min($day, $lastDay))->toDateString(),
                    'notes' => 'Sample data for the SpendSense demo.',
                ]);
            }
        }

        foreach (['Food' => 10000, 'Transport' => 4000, 'Bills' => 3000] as $category => $amount) {
            $budgetCategory = $categories[$category] ?? throw new LogicException("Unknown demo category: {$category}");

            $demo->budgets()->create([
                'category_id' => $budgetCategory->id,
                'amount' => $amount,
                'month' => $today->month,
                'year' => $today->year,
            ]);
        }
    }
}
