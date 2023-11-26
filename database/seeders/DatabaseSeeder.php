<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Responsibility;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(10)
            ->has(Company::factory()
                ->count(2)
                ->has(Team::factory()
                    ->count(3))
                ->has(Role::factory()
                    ->count(4)
                    ->has(Responsibility::factory()
                        ->count(2))))
            ->create();

        Employee::factory()
            ->count(20)
            ->create()
            ->each(function ($employee) {
                do {
                    $role = Role::inRandomOrder()->first();
                    $team = Team::inRandomOrder()->first();
                } while ($role->company_id !== $team->company_id);

                $employee->update([
                    'role_id' => $role->id,
                    'team_id' => $team->id,
                ]);
            });
    }
}
