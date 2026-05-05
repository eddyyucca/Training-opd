<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use SimpleXMLElement;
use Carbon\Carbon;

class ImportTrainingWorkbook extends Command
{
    protected $signature = 'app:import-training-workbook
                            {path? : Path file Excel training}
                            {--fresh : Hapus data training dan karyawan sebelum import}';

    protected $description = 'Import data Training Record.xlsx ke tabel training dan karyawan';

    public function handle()
    {
        $path = $this->argument('path') ?: 'C:\\Users\\eddy.saputra\\Downloads\\Training Record.xlsx';

        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $rows = $this->readRawDataSheet($path);

        if ($rows === []) {
            $this->warn('Tidak ada data yang bisa diimport dari sheet Raw Data.');

            return self::INVALID;
        }

        DB::transaction(function () use ($rows) {
            if ($this->option('fresh')) {
                DB::table('employee_training')->truncate();
                Training::query()->delete();
                Employee::query()->delete();
            }

            foreach ($rows as $row) {
                $employee = Employee::query()->updateOrCreate(
                    ['nik' => $row['nik']],
                    [
                        'name' => $row['participant_name'],
                        'gender' => $row['gender'],
                        'division' => $row['division'],
                        'department' => $row['department'],
                        'position_title' => $row['position_title'],
                        'company' => $row['company'],
                        'job_level_group' => $this->resolveJobLevelGroup($row['position_title']),
                        'is_active' => true,
                    ]
                );

                $training = Training::query()->firstOrCreate(
                    [
                        'year' => $row['year'],
                        'name' => $row['training_name'],
                        'provider' => $row['provider'],
                        'start_date' => $row['start_date'],
                        'end_date' => $row['end_date'],
                    ],
                    [
                        'training_classification' => $row['training_classification'],
                        'training_sub_classification' => $row['training_sub_classification'],
                        'category' => $row['category'],
                        'training_type' => $row['training_type'],
                        'month' => $row['month'],
                        'hours' => $row['hours'],
                        'days' => $row['days'],
                    ]
                );

                $training->employees()->syncWithoutDetaching([$employee->id]);
            }
        });

        $this->info('Import selesai.');
        $this->line('Total karyawan: '.Employee::count());
        $this->line('Total training: '.Training::count());
        $this->line('Total partisipasi: '.DB::table('employee_training')->count());

        return self::SUCCESS;
    }

    private function readRawDataSheet(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Workbook tidak bisa dibuka.');
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet3.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new \RuntimeException('Sheet Raw Data tidak ditemukan.');
        }

        $xml = new SimpleXMLElement($sheetXml);
        $rows = [];

        foreach ($xml->sheetData->row as $index => $row) {
            $mapped = $this->mapRow($row, $sharedStrings);

            if ($index === 0) {
                continue;
            }

            if ($mapped['nik'] === '' && $mapped['participant_name'] === '' && $mapped['training_name'] === '') {
                continue;
            }

            $rows[] = $mapped;
        }

        return $rows;
    }

    private function mapRow(SimpleXMLElement $row, array $sharedStrings): array
    {
        $cells = [];

        foreach ($row->c as $cell) {
            $reference = (string) $cell['r'];
            $column = preg_replace('/\d+/', '', $reference);
            $cells[$column] = $this->readCellValue($cell, $sharedStrings);
        }

        return [
            'year' => $this->nullableInt($cells['B'] ?? null),
            'training_name' => trim((string) ($cells['C'] ?? '')),
            'nik' => trim((string) ($cells['D'] ?? '')),
            'participant_name' => trim((string) ($cells['E'] ?? '')),
            'gender' => trim((string) ($cells['F'] ?? '')),
            'division' => trim((string) ($cells['G'] ?? '')),
            'department' => trim((string) ($cells['H'] ?? '')),
            'position_title' => trim((string) ($cells['I'] ?? '')),
            'company' => trim((string) ($cells['J'] ?? '')),
            'training_classification' => trim((string) ($cells['K'] ?? '')),
            'training_sub_classification' => trim((string) ($cells['L'] ?? '')),
            'category' => trim((string) ($cells['M'] ?? '')),
            'training_type' => trim((string) ($cells['N'] ?? '')),
            'provider' => trim((string) ($cells['O'] ?? '')),
            'month' => trim((string) ($cells['P'] ?? '')),
            'start_date' => $this->excelDateToSql($cells['Q'] ?? null),
            'end_date' => $this->excelDateToSql($cells['R'] ?? null),
            'hours' => $this->nullableFloat($cells['S'] ?? null) ?? 0,
            'days' => $this->nullableInt($cells['T'] ?? null) ?? 0,
        ];
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($sharedStringsXml === false) {
            return [];
        }

        $xml = new SimpleXMLElement($sharedStringsXml);
        $values = [];

        foreach ($xml->si as $item) {
            if (isset($item->t)) {
                $values[] = (string) $item->t;
                continue;
            }

            $text = '';
            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }
            $values[] = $text;
        }

        return $values;
    }

    private function readCellValue(SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) ($cell['t'] ?? '');
        $value = isset($cell->v) ? (string) $cell->v : '';

        if ($type === 's') {
            return $sharedStrings[(int) $value] ?? '';
        }

        return $value;
    }

    private function excelDateToSql(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) floor((float) $value))->toDateString();
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function nullableInt(?string $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function nullableFloat(?string $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function resolveJobLevelGroup(string $positionTitle): string
    {
        $value = str($positionTitle)->lower()->value();

        if ($value === '') {
            return 'Staff & Non Staff';
        }

        foreach (['general manager', 'gm', 'director', 'president', 'chief', 'head'] as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'Senior Management';
            }
        }

        foreach (['manager', 'assistant manager', 'asst manager', 'supervisor', 'spv', 'superintendent', 'supt'] as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'Manager, Asst Manager, & Supervisor';
            }
        }

        return 'Staff & Non Staff';
    }
}
