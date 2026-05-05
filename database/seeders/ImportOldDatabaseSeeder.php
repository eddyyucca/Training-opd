<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportOldDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sqlitePath = database_path('database.sqlite');

        if (! File::exists($sqlitePath)) {
            $this->command?->error("SQLite file not found: {$sqlitePath}");

            return;
        }

        $sqlite = new \PDO('sqlite:' . $sqlitePath);
        $sqlite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $sqlite->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $tables = [
            'departments',
            'training_categories',
            'training_types',
            'training_providers',
            'training_classifications',
            'training_sub_classifications',
            'users',
            'employees',
            'trainings',
            'employee_training',
        ];

        Schema::disableForeignKeyConstraints();

        try {
            foreach (array_reverse($tables) as $table) {
                DB::table($table)->truncate();
            }

            $departmentMap = $this->importDepartments($sqlite);
            $categoryMap = $this->importSimpleNameTable($sqlite, 'training_categories');
            $typeMap = $this->importSimpleNameTable($sqlite, 'training_types');
            $providerMap = $this->importSimpleNameTable($sqlite, 'training_providers');
            $classificationMap = $this->importSimpleNameTable($sqlite, 'training_classifications');
            $subClassificationMap = $this->importSubClassifications($sqlite, $classificationMap);

            $this->importUsers($sqlite, $departmentMap);
            $this->importPlainTable($sqlite, 'employees');
            $this->importTrainings(
                $sqlite,
                $departmentMap,
                $categoryMap,
                $typeMap,
                $providerMap,
                $classificationMap,
                $subClassificationMap
            );
            $this->importPlainTable($sqlite, 'employee_training');
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $this->command?->info("Imported {$count} rows into {$table}");
        }
    }

    private function importDepartments(\PDO $sqlite): array
    {
        $rows = $sqlite->query('SELECT * FROM departments ORDER BY id')->fetchAll();
        $canonicalRows = [];
        $map = [];
        $seen = [];

        foreach ($rows as $row) {
            $key = $this->normalize((string) $row['code']);

            if (! isset($seen[$key])) {
                $seen[$key] = (int) $row['id'];
                $canonicalRows[] = $row;
            }

            $map[(int) $row['id']] = $seen[$key];
        }

        $this->insertChunked('departments', $canonicalRows);

        return $map;
    }

    private function importSimpleNameTable(\PDO $sqlite, string $table): array
    {
        $rows = $sqlite->query("SELECT * FROM {$table} ORDER BY id")->fetchAll();
        $canonicalRows = [];
        $map = [];
        $seen = [];

        foreach ($rows as $row) {
            $key = $this->normalize((string) $row['name']);

            if (! isset($seen[$key])) {
                $seen[$key] = (int) $row['id'];
                $canonicalRows[] = $row;
            }

            $map[(int) $row['id']] = $seen[$key];
        }

        $this->insertChunked($table, $canonicalRows);

        return $map;
    }

    private function importSubClassifications(\PDO $sqlite, array $classificationMap): array
    {
        $rows = $sqlite->query('SELECT * FROM training_sub_classifications ORDER BY id')->fetchAll();
        $canonicalRows = [];
        $map = [];
        $seen = [];

        foreach ($rows as $row) {
            $canonicalClassificationId = $classificationMap[(int) $row['training_classification_id']] ?? null;

            if (! $canonicalClassificationId) {
                continue;
            }

            $row['training_classification_id'] = $canonicalClassificationId;
            $key = $canonicalClassificationId.'|'.$this->normalize((string) $row['name']);

            if (! isset($seen[$key])) {
                $seen[$key] = (int) $row['id'];
                $canonicalRows[] = $row;
            }

            $map[(int) $row['id']] = $seen[$key];
        }

        $this->insertChunked('training_sub_classifications', $canonicalRows);

        return $map;
    }

    private function importUsers(\PDO $sqlite, array $departmentMap): void
    {
        $rows = $sqlite->query('SELECT * FROM users ORDER BY id')->fetchAll();
        $rows = array_map(function (array $row) use ($departmentMap) {
            $row['department_id'] = $row['department_id']
                ? ($departmentMap[(int) $row['department_id']] ?? null)
                : null;

            return $row;
        }, $rows);

        $this->insertChunked('users', $rows);
    }

    private function importTrainings(
        \PDO $sqlite,
        array $departmentMap,
        array $categoryMap,
        array $typeMap,
        array $providerMap,
        array $classificationMap,
        array $subClassificationMap
    ): void {
        $rows = $sqlite->query('SELECT * FROM trainings ORDER BY id')->fetchAll();

        $rows = array_map(function (array $row) use (
            $departmentMap,
            $categoryMap,
            $typeMap,
            $providerMap,
            $classificationMap,
            $subClassificationMap
        ) {
            $row['department_id'] = $row['department_id']
                ? ($departmentMap[(int) $row['department_id']] ?? null)
                : null;
            $row['training_category_id'] = $row['training_category_id']
                ? ($categoryMap[(int) $row['training_category_id']] ?? null)
                : null;
            $row['training_type_id'] = $row['training_type_id']
                ? ($typeMap[(int) $row['training_type_id']] ?? null)
                : null;
            $row['training_provider_id'] = $row['training_provider_id']
                ? ($providerMap[(int) $row['training_provider_id']] ?? null)
                : null;
            $row['training_classification_id'] = $row['training_classification_id']
                ? ($classificationMap[(int) $row['training_classification_id']] ?? null)
                : null;
            $row['training_sub_classification_id'] = $row['training_sub_classification_id']
                ? ($subClassificationMap[(int) $row['training_sub_classification_id']] ?? null)
                : null;

            return $row;
        }, $rows);

        $this->insertChunked('trainings', $rows);
    }

    private function importPlainTable(\PDO $sqlite, string $table): void
    {
        $rows = $sqlite->query("SELECT * FROM {$table} ORDER BY id")->fetchAll();
        $this->insertChunked($table, $rows);
    }

    private function insertChunked(string $table, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
