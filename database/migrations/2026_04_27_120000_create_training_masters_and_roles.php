<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('training_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('training_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('training_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('training_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('training_sub_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_classification_id')
                ->constrained('training_classifications')
                ->cascadeOnDelete();

            $table->string('name');

            $table->unique(
                ['training_classification_id', 'name'],
                'tr_sub_class_classid_name_uq'
            );

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('opd')->after('password');
            $table->foreignId('department_id')
                ->nullable()
                ->after('role')
                ->constrained('departments')
                ->nullOnDelete();
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('attendance_token')
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('training_category_id')
                ->nullable()
                ->after('department_id')
                ->constrained('training_categories')
                ->nullOnDelete();

            $table->foreignId('training_type_id')
                ->nullable()
                ->after('training_category_id')
                ->constrained('training_types')
                ->nullOnDelete();

            $table->foreignId('training_provider_id')
                ->nullable()
                ->after('training_type_id')
                ->constrained('training_providers')
                ->nullOnDelete();

            $table->foreignId('training_classification_id')
                ->nullable()
                ->after('training_provider_id')
                ->constrained('training_classifications')
                ->nullOnDelete();

            $table->foreignId('training_sub_classification_id')
                ->nullable()
                ->after('training_classification_id')
                ->constrained('training_sub_classifications')
                ->nullOnDelete();

            $table->time('start_time')->nullable()->after('start_date');
            $table->time('end_time')->nullable()->after('end_date');
        });

        $now = now();

        $departments = collect([
            ['code' => 'OPD', 'name' => 'Organization and People Development'],
            ['code' => 'OHS', 'name' => 'Occupational Health and Safety'],
        ]);

        $existingDepartments = DB::table('employees')
            ->select('department')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        foreach ($existingDepartments as $departmentName) {
            $code = strtoupper(preg_replace('/[^A-Z0-9]+/', '', substr($departmentName, 0, 6)));

            $departments->push([
                'code' => $code ?: strtoupper(substr(md5($departmentName), 0, 6)),
                'name' => $departmentName,
            ]);
        }

        $departmentMap = [];

        foreach ($departments->unique('code')->values() as $department) {
            $id = DB::table('departments')->insertGetId([
                'code' => $department['code'],
                'name' => $department['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $departmentMap[$department['name']] = $id;
            $departmentMap[$department['code']] = $id;
        }

        $categories = DB::table('trainings')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $name) {
            DB::table('training_categories')->updateOrInsert(
                ['name' => $name],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $types = DB::table('trainings')
            ->select('training_type')
            ->whereNotNull('training_type')
            ->where('training_type', '!=', '')
            ->distinct()
            ->pluck('training_type');

        foreach ($types as $name) {
            DB::table('training_types')->updateOrInsert(
                ['name' => $name],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $providers = DB::table('trainings')
            ->select('provider')
            ->whereNotNull('provider')
            ->where('provider', '!=', '')
            ->distinct()
            ->pluck('provider');

        foreach ($providers as $name) {
            DB::table('training_providers')->updateOrInsert(
                ['name' => $name],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $classificationMap = [];

        $classifications = DB::table('trainings')
            ->select('training_classification')
            ->whereNotNull('training_classification')
            ->where('training_classification', '!=', '')
            ->distinct()
            ->pluck('training_classification');

        foreach ($classifications as $name) {
            $classificationMap[$name] = DB::table('training_classifications')->insertGetId([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('trainings')
            ->select('training_classification', 'training_sub_classification')
            ->whereNotNull('training_classification')
            ->where('training_classification', '!=', '')
            ->whereNotNull('training_sub_classification')
            ->where('training_sub_classification', '!=', '')
            ->distinct()
            ->orderBy('training_classification')
            ->get()
            ->each(function ($row) use ($classificationMap, $now) {
                $classificationId = $classificationMap[$row->training_classification] ?? null;

                if (! $classificationId) {
                    return;
                }

                DB::table('training_sub_classifications')->updateOrInsert(
                    [
                        'training_classification_id' => $classificationId,
                        'name' => $row->training_sub_classification,
                    ],
                    [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            });

        $categoryMap = DB::table('training_categories')->pluck('id', 'name');
        $typeMap = DB::table('training_types')->pluck('id', 'name');
        $providerMap = DB::table('training_providers')->pluck('id', 'name');

        $subClassificationMap = DB::table('training_sub_classifications')
            ->get()
            ->keyBy(fn ($row) => $row->training_classification_id . '|' . $row->name);

        DB::table('trainings')
            ->select('id', 'category', 'training_type', 'provider', 'training_classification', 'training_sub_classification')
            ->orderBy('id')
            ->get()
            ->each(function ($training) use ($categoryMap, $typeMap, $providerMap, $classificationMap, $subClassificationMap) {
                $classificationId = $classificationMap[$training->training_classification] ?? null;

                $subClassificationId = $classificationId && $training->training_sub_classification
                    ? optional($subClassificationMap->get($classificationId . '|' . $training->training_sub_classification))->id
                    : null;

                DB::table('trainings')->where('id', $training->id)->update([
                    'training_category_id' => $categoryMap[$training->category] ?? null,
                    'training_type_id' => $typeMap[$training->training_type] ?? null,
                    'training_provider_id' => $providerMap[$training->provider] ?? null,
                    'training_classification_id' => $classificationId,
                    'training_sub_classification_id' => $subClassificationId,
                ]);
            });

        $opdId = DB::table('departments')->where('code', 'OPD')->value('id');

        DB::table('users')->whereNull('department_id')->update([
            'role' => 'opd',
            'department_id' => $opdId,
        ]);

        DB::table('trainings')->whereNull('department_id')->update([
            'department_id' => $opdId,
        ]);
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_sub_classification_id');
            $table->dropConstrainedForeignId('training_classification_id');
            $table->dropConstrainedForeignId('training_provider_id');
            $table->dropConstrainedForeignId('training_type_id');
            $table->dropConstrainedForeignId('training_category_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['start_time', 'end_time']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn('role');
        });

        Schema::dropIfExists('training_sub_classifications');
        Schema::dropIfExists('training_classifications');
        Schema::dropIfExists('training_providers');
        Schema::dropIfExists('training_types');
        Schema::dropIfExists('training_categories');
        Schema::dropIfExists('departments');
    }
};