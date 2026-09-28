<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\OpeningBalanceCollection;
use App\Models\SchoolOpeningBalance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpeningBalanceSampleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'Administrator'))->first()
            ?? User::first();

        $accountsUser = User::whereHas('role', fn($q) => $q->where('name', 'Accounts'))->first()
            ?? $admin;

        $sps = User::salesPersons()->active()->get();
        $sp1 = $sps->get(0) ?? $admin;
        $sp2 = $sps->get(1) ?? $admin;
        $sp3 = $sps->get(2) ?? $admin;

        $schools = Customer::take(4)->get();
        if ($schools->isEmpty()) {
            return;
        }

        // Clean up previous sample opening balances for these schools
        SchoolOpeningBalance::whereIn('customer_id', $schools->pluck('id'))->delete();

        // ── 1. School 1 (Session 2026-27): ₹3,50,000 with 2 payments (₹1.5L paid, ₹2L pending)
        $sch1 = $schools->get(0);
        if ($sch1) {
            $ob1 = SchoolOpeningBalance::create([
                'customer_id'         => $sch1->id,
                'session_year'        => 2026,
                'user_id'             => $sp1->id,
                'opening_amount'      => 350000.00,
                'opening_billed'      => 350000.00,
                'opening_collected'   => 0,
                'opening_outstanding' => 350000.00,
                'source'              => 'manual',
                'notes'               => 'Carry forward from 2025-26 session (unsettled books & kit PO balance)',
                'entered_by'          => $admin->id,
            ]);

            OpeningBalanceCollection::create([
                'opening_balance_id' => $ob1->id,
                'collected_amount'   => 100000.00,
                'payment_mode'       => 'neft',
                'reference_number'   => 'NEFT2026071501',
                'remarks'            => 'First installment received',
                'collected_by'       => $accountsUser->id,
                'collected_at'       => '2026-07-15',
            ]);

            OpeningBalanceCollection::create([
                'opening_balance_id' => $ob1->id,
                'collected_amount'   => 50000.00,
                'payment_mode'       => 'cheque',
                'reference_number'   => 'CHQ-982143',
                'remarks'            => 'Part payment cheque cleared',
                'collected_by'       => $accountsUser->id,
                'collected_at'       => '2026-08-10',
            ]);
        }

        // ── 2. School 2 (Session 2026-27): ₹5,00,000 with 1 payment (₹2L paid, ₹3L pending)
        $sch2 = $schools->get(1);
        if ($sch2) {
            $ob2 = SchoolOpeningBalance::create([
                'customer_id'         => $sch2->id,
                'session_year'        => 2026,
                'user_id'             => $sp2->id,
                'opening_amount'      => 500000.00,
                'opening_billed'      => 500000.00,
                'opening_collected'   => 0,
                'opening_outstanding' => 500000.00,
                'source'              => 'manual',
                'notes'               => 'Carry forward from 2025-26 session (unbilled/uncollected balance)',
                'entered_by'          => $admin->id,
            ]);

            OpeningBalanceCollection::create([
                'opening_balance_id' => $ob2->id,
                'collected_amount'   => 200000.00,
                'payment_mode'       => 'rtgs',
                'reference_number'   => 'RTGS2026080109',
                'remarks'            => 'Direct bank transfer from school account',
                'collected_by'       => $accountsUser->id,
                'collected_at'       => '2026-08-01',
            ]);
        }

        // ── 3. School 3 (Session 2026-27): ₹1,80,000 full pending (0 collected)
        $sch3 = $schools->get(2);
        if ($sch3) {
            SchoolOpeningBalance::create([
                'customer_id'         => $sch3->id,
                'session_year'        => 2026,
                'user_id'             => $sp3->id,
                'opening_amount'      => 180000.00,
                'opening_billed'      => 180000.00,
                'opening_collected'   => 0.00,
                'opening_outstanding' => 180000.00,
                'source'              => 'manual',
                'notes'               => 'Carry forward balance - payment follow-up in progress',
                'entered_by'          => $admin->id,
            ]);
        }

        // ── 4. School 4 (Session 2025-26): ₹2,50,000 fully cleared in previous session
        $sch4 = $schools->get(3);
        if ($sch4) {
            $ob4 = SchoolOpeningBalance::create([
                'customer_id'         => $sch4->id,
                'session_year'        => 2025,
                'user_id'             => $sp1->id,
                'opening_amount'      => 250000.00,
                'opening_billed'      => 250000.00,
                'opening_collected'   => 0,
                'opening_outstanding' => 250000.00,
                'source'              => 'auto',
                'notes'               => 'Auto carry forward from session 2024-25',
                'entered_by'          => $admin->id,
            ]);

            OpeningBalanceCollection::create([
                'opening_balance_id' => $ob4->id,
                'collected_amount'   => 250000.00,
                'payment_mode'       => 'neft',
                'reference_number'   => 'NEFT2025112044',
                'remarks'            => 'Full settlement cleared in previous session',
                'collected_by'       => $accountsUser->id,
                'collected_at'       => '2025-11-20',
            ]);
        }
    }
}
