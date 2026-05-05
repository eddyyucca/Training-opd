<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->decimal('cost_per_person', 15, 2)->nullable()->after('quota');
            $table->string('pr_number', 100)->nullable()->after('cost_per_person');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['cost_per_person', 'pr_number']);
        });
    }
};
