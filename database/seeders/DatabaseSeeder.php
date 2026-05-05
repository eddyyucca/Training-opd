<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $opdDepartment = Department::query()->firstOrCreate(
            ['code' => 'OPD'],
            ['name' => 'Organization and People Development']
        );

        $ohsDepartment = Department::query()->firstOrCreate(
            ['code' => 'OHS'],
            ['name' => 'Occupational Health and Safety']
        );

        User::query()->updateOrCreate(
            ['email' => 'opd@local.test'],
            [
                'name' => 'OPD Admin',
                'password' => 'password',
                'role' => 'opd',
                'department_id' => $opdDepartment->id,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'ohs@local.test'],
            [
                'name' => 'OHS User',
                'password' => 'password',
                'role' => 'department',
                'department_id' => $ohsDepartment->id,
            ]
        );
    }
}
