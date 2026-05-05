<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->uuid('registration_token')->nullable()->unique()->after('notes');
            $table->uuid('attendance_token')->nullable()->unique()->after('registration_token');
        });

        Schema::table('employee_training', function (Blueprint $table) {
            $table->timestamp('registered_at')->nullable()->after('training_id');
            $table->timestamp('attended_at')->nullable()->after('registered_at');
        });

        DB::table('trainings')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->each(function ($training) {
                DB::table('trainings')
                    ->where('id', $training->id)
                    ->update([
                        'registration_token' => (string) Str::uuid(),
                        'attendance_token' => (string) Str::uuid(),
                    ]);
            });

        DB::table('employee_training')
            ->whereNull('registered_at')
            ->update([
                'registered_at' => DB::raw('created_at'),
                'attended_at' => DB::raw('created_at'),
            ]);

    }

    public function down(): void
    {
        Schema::table('employee_training', function (Blueprint $table) {
            $table->dropColumn(['registered_at', 'attended_at']);
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropUnique(['registration_token']);
            $table->dropUnique(['attendance_token']);
            $table->dropColumn(['registration_token', 'attendance_token']);
        });
    }
};
