<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Expense;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single treasurer user
        $user = User::query()->firstOrCreate(
            ['email' => 'treasurer@example.com'],
            [
                'name' => 'Skarbnik Klasowy',
                'password' => 'password',
                'role' => 'collector',
            ]
        );

        // Create an admin user for management purposes
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        // Create collections for that user
        $collections = collect([
            ['name' => 'Zbiórka ogólna - Jesień', 'school_year' => '2025', 'status' => 'active'],
            ['name' => 'Wycieczka - Zima', 'school_year' => '2025', 'status' => 'pending'],
            ['name' => 'Dni Sportu - Wiosna', 'school_year' => '2026', 'status' => 'pending'],
        ])->map(function (array $attrs) use ($user) {
            return Collection::query()->firstOrCreate(
                ['user_id' => $user->id, 'name' => $attrs['name']],
                [
                    'school_year' => $attrs['school_year'] ?? '2025',
                    'description' => null,
                    'status' => $attrs['status'] ?? 'pending',
                    'is_active' => true,
                ]
            );
        });

        $today = Carbon::now();

        // For each collection create guardians, payments and expenses
        foreach ($collections as $collection) {
            $guardians = Guardian::factory()
                ->count(random_int(3, 5))
                ->for($collection)
                ->create();

            // Each guardian 1–2 payments linked to both guardian and collection
            foreach ($guardians as $guardian) {
                $paymentsCount = random_int(1, 2);
                for ($i = 0; $i < $paymentsCount; $i++) {
                    Payment::query()->create([
                        'collection_id' => $collection->id,
                        'guardian_id' => $guardian->id,
                        'amount' => round(mt_rand(2000, 15000) / 100, 2), // 20.00–150.00
                        'payment_date' => $today->copy()->subDays(mt_rand(0, 60))->toDateString(),
                        'description' => 'Wpłata rodzica',
                    ]);
                }
            }

            // expenses per collection
            $expensesCount = random_int(2, 4);
            for ($i = 0; $i < $expensesCount; $i++) {
                Expense::query()->create([
                    'collection_id' => $collection->id,
                    'amount' => round(mt_rand(300, 10000) / 100, 2), // 3.00–100.00
                    'expense_date' => $today->copy()->subDays(mt_rand(0, 60))->toDateString(),
                    'description' => 'Wydatek',
                    'approved' => (bool) random_int(0, 1),
                ]);
            }
        }
    }
}
