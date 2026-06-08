<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Repayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = ['ACC-001', 'ACC-002', 'ACC-003'];

        // Seed transactions
        foreach ($accounts as $account) {
            for ($i = 1; $i <= 5; $i++) {
                Transaction::create([
                    'account_id'         => $account,
                    'type'               => $i % 2 === 0 ? 'credit' : 'debit',
                    'amount'             => rand(100, 10000) * 1000,
                    'description'        => $i % 2 === 0 ? 'Transfer masuk' : 'Transfer keluar',
                    'reference_number'   => 'REF-' . strtoupper(Str::random(10)),
                    'transaction_date'   => now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // Seed repayments
        foreach ($accounts as $account) {
            $loanAmount = rand(5, 50) * 1000000;
            for ($i = 1; $i <= 3; $i++) {
                Repayment::create([
                    'account_id'         => $account,
                    'loan_amount'        => $loanAmount,
                    'repayment_amount'   => $loanAmount / 12,
                    'installment_number' => $i,
                    'status'             => $i <= 2 ? 'paid' : 'pending',
                    'due_date'           => now()->subMonths(3 - $i),
                    'paid_at'            => $i <= 2 ? now()->subMonths(3 - $i) : null,
                ]);
            }
        }
    }
}
