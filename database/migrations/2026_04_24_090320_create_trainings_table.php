<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('name');
            $table->string('training_classification')->nullable();
            $table->string('training_sub_classification')->nullable();
            $table->string('category')->nullable();
            $table->string('training_type')->nullable();
            $table->string('provider')->nullable();
            $table->string('month', 20)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('hours', 8, 2)->default(0);
            $table->unsignedSmallInteger('days')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
