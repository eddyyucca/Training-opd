<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = $this->availableYears();
        $selectedYear = $request->integer('year') ?: (int) ($availableYears->first() ?: now()->year);
        $selectedMonth = $request->filled('month') ? $request->integer('month') : null;
        $availableWeeks = $this->availableWeeks($selectedYear, $selectedMonth);
        $selectedWeek = $request->filled('week') ? $request->integer('week') : null;

        if ($selectedWeek && ! $availableWeeks->contains(fn ($week) => $week['value'] === $selectedWeek)) {
            $selectedWeek = null;
        }

        $hasAttendanceColumn = Schema::hasColumn('employee_training', 'attended_at');

        $baseQuery = $this->applyPeriodFilter(
            $this->trainingScope(),
            $selectedYear,
            $selectedMonth,
            $selectedWeek
        );

        $attendanceQuery = $this->applyPeriodFilter(
            $this->trainingScope()->join('employee_training', 'trainings.id', '=', 'employee_training.training_id'),
            $selectedYear,
            $selectedMonth,
            $selectedWeek,
            'trainings.start_date'
        );

        if ($hasAttendanceColumn) {
            $attendanceQuery->whereNotNull('employee_training.attended_at');
        }

        $totalTrainings = (clone $baseQuery)->count();
        $totalHours = (clone $attendanceQuery)->sum('trainings.hours');
        $totalParticipants = (clone $attendanceQuery)->count();
        $uniqueEmployees = (clone $attendanceQuery)
            ->distinct('employee_training.employee_id')
            ->count('employee_training.employee_id');

        $summaryQuery = $this->applyPeriodFilter(
            $this->trainingScope()
                ->join('employee_training', 'trainings.id', '=', 'employee_training.training_id')
                ->join('employees', 'employees.id', '=', 'employee_training.employee_id'),
            $selectedYear,
            $selectedMonth,
            $selectedWeek,
            'trainings.start_date'
        );

        if ($hasAttendanceColumn) {
            $summaryQuery->whereNotNull('employee_training.attended_at');
        }

        $genderSummary = (clone $summaryQuery)
            ->selectRaw("
                COALESCE(NULLIF(employees.gender, ''), 'Unknown') as group_label,
                SUM(trainings.hours) as total_hours,
                COUNT(DISTINCT employees.id) as employee_count
            ")
            ->groupBy('group_label')
            ->orderByRaw("CASE group_label WHEN 'Male' THEN 1 WHEN 'Female' THEN 2 ELSE 3 END")
            ->get()
            ->map(fn ($item) => [
                'label' => $item->group_label,
                'total_hours' => (float) $item->total_hours,
                'employee_count' => (int) $item->employee_count,
                'average_hours' => $item->employee_count > 0 ? (float) $item->total_hours / $item->employee_count : 0,
            ]);

        $positionSummary = (clone $summaryQuery)
            ->selectRaw("
                COALESCE(NULLIF(employees.job_level_group, ''), 'Staff & Non Staff') as group_label,
                SUM(trainings.hours) as total_hours,
                COUNT(DISTINCT employees.id) as employee_count
            ")
            ->groupBy('group_label')
            ->orderByRaw("
                CASE group_label
                    WHEN 'Senior Management' THEN 1
                    WHEN 'Manager, Asst Manager, & Supervisor' THEN 2
                    ELSE 3
                END
            ")
            ->get()
            ->map(fn ($item) => [
                'label' => $item->group_label,
                'total_hours' => (float) $item->total_hours,
                'employee_count' => (int) $item->employee_count,
                'average_hours' => $item->employee_count > 0 ? (float) $item->total_hours / $item->employee_count : 0,
            ]);

        $departmentSummary = (clone $summaryQuery)
            ->selectRaw("
                COALESCE(NULLIF(employees.department, ''), 'Belum Diisi') as group_label,
                SUM(trainings.hours) as total_hours,
                COUNT(DISTINCT employees.id) as employee_count,
                COUNT(employee_training.training_id) as participation_count
            ")
            ->groupBy('group_label')
            ->orderByDesc('total_hours')
            ->orderBy('group_label')
            ->get()
            ->map(fn ($item) => [
                'label' => $item->group_label,
                'total_hours' => (float) $item->total_hours,
                'employee_count' => (int) $item->employee_count,
                'participation_count' => (int) $item->participation_count,
                'average_hours' => $item->employee_count > 0 ? (float) $item->total_hours / $item->employee_count : 0,
            ]);

        $classificationSummary = (clone $baseQuery)
            ->select('training_classification', DB::raw('count(*) as total'))
            ->groupBy('training_classification')
            ->orderByDesc('total')
            ->get();

        $recentTrainings = (clone $baseQuery)
            ->withCount('attendedEmployees')
            ->latest('start_date')
            ->latest('id')
            ->take(5)
            ->get();

        $providerSummary = (clone $baseQuery)
            ->selectRaw("COALESCE(NULLIF(provider, ''), 'Belum diisi') as provider_label, COUNT(*) as total")
            ->groupBy('provider_label')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $upcomingTrainings = (clone $baseQuery)
            ->whereDate('start_date', '>', now()->toDateString())
            ->count();

        $runningTrainings = (clone $baseQuery)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->count();

        $runningTrainingList = (clone $baseQuery)
            ->with('department')
            ->withCount(['registeredEmployees', 'attendedEmployees'])
            ->whereDate('start_date', '<=', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $completedTrainings = (clone $baseQuery)
            ->whereDate('end_date', '<', now()->toDateString())
            ->count();

        $trendRows = (clone $summaryQuery)
            ->select('trainings.start_date', 'trainings.hours')
            ->get();

        $trendSeries = $this->buildTrendSeries(
            $trendRows,
            $selectedYear,
            $selectedMonth,
            $selectedWeek
        );

        // Cost summary: SUM(cost_per_person) per attended row = total cost across all attendees
        $totalCost = (clone $attendanceQuery)
            ->whereNotNull('trainings.cost_per_person')
            ->sum('trainings.cost_per_person');

        // Training cost breakdown (per training with cost)
        $trainingCostSummary = $this->applyPeriodFilter(
            $this->trainingScope()->whereNotNull('cost_per_person'),
            $selectedYear,
            $selectedMonth,
            $selectedWeek
        )
            ->withCount('attendedEmployees')
            ->orderByDesc('start_date')
            ->take(10)
            ->get()
            ->map(fn ($t) => [
                'name' => $t->name,
                'cost_per_person' => (float) $t->cost_per_person,
                'attended_count' => $t->attended_employees_count,
                'total_cost' => (float) $t->cost_per_person * $t->attended_employees_count,
            ]);

        // Internal vs External split
        $internalQuery = $this->applyPeriodFilter(
            $this->trainingScope()
                ->join('employee_training as et_int', 'trainings.id', '=', 'et_int.training_id')
                ->join('employees as emp_int', 'emp_int.id', '=', 'et_int.employee_id')
                ->where('emp_int.is_external', false),
            $selectedYear,
            $selectedMonth,
            $selectedWeek,
            'trainings.start_date'
        );

        $externalQuery = $this->applyPeriodFilter(
            $this->trainingScope()
                ->join('employee_training as et_ext', 'trainings.id', '=', 'et_ext.training_id')
                ->join('employees as emp_ext', 'emp_ext.id', '=', 'et_ext.employee_id')
                ->where('emp_ext.is_external', true),
            $selectedYear,
            $selectedMonth,
            $selectedWeek,
            'trainings.start_date'
        );

        if ($hasAttendanceColumn) {
            $internalQuery->whereNotNull('et_int.attended_at');
            $externalQuery->whereNotNull('et_ext.attended_at');
        }

        $internalStats = [
            'participants' => (clone $internalQuery)->count(),
            'unique_employees' => (clone $internalQuery)->distinct('et_int.employee_id')->count('et_int.employee_id'),
            'total_hours' => (float) (clone $internalQuery)->sum('trainings.hours'),
        ];

        $externalStats = [
            'participants' => (clone $externalQuery)->count(),
            'unique_employees' => (clone $externalQuery)->distinct('et_ext.employee_id')->count('et_ext.employee_id'),
            'total_hours' => (float) (clone $externalQuery)->sum('trainings.hours'),
        ];

        return view('dashboard', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedWeek' => $selectedWeek,
            'availableYears' => $availableYears,
            'availableMonths' => $this->availableMonths(),
            'availableWeeks' => $availableWeeks,
            'periodLabel' => $this->periodLabel($selectedYear, $selectedMonth, $selectedWeek),
            'totalTrainings' => $totalTrainings,
            'totalHours' => (float) $totalHours,
            'totalParticipants' => $totalParticipants,
            'uniqueEmployees' => $uniqueEmployees,
            'averageHours' => $uniqueEmployees > 0 ? (float) $totalHours / $uniqueEmployees : 0,
            'employeeMasterCount' => $this->employeeScope()->count(),
            'genderSummary' => $genderSummary,
            'positionSummary' => $positionSummary,
            'departmentSummary' => $departmentSummary,
            'trendSeries' => $trendSeries,
            'classificationSummary' => $classificationSummary,
            'recentTrainings' => $recentTrainings,
            'providerSummary' => $providerSummary,
            'upcomingTrainings' => $upcomingTrainings,
            'runningTrainings' => $runningTrainings,
            'runningTrainingList' => $runningTrainingList,
            'completedTrainings' => $completedTrainings,
            'totalCost' => (float) $totalCost,
            'trainingCostSummary' => $trainingCostSummary,
            'internalStats' => $internalStats,
            'externalStats' => $externalStats,
        ]);
    }

    public function departmentReport(Request $request)
    {
        $selectedYear = $request->integer('year');
        $selectedDepartment = $request->string('department')->toString();
        $hasAttendanceColumn = Schema::hasColumn('employee_training', 'attended_at');

        $summaryQuery = $this->trainingScope()
            ->join('employee_training', 'trainings.id', '=', 'employee_training.training_id')
            ->join('employees', 'employees.id', '=', 'employee_training.employee_id')
            ->when($selectedYear, fn ($query) => $query->where('trainings.year', $selectedYear))
            ->when($selectedDepartment !== '', fn ($query) => $query->whereRaw(
                "COALESCE(NULLIF(employees.department, ''), 'Belum Diisi') = ?",
                [$selectedDepartment]
            ));

        if ($hasAttendanceColumn) {
            $summaryQuery->whereNotNull('employee_training.attended_at');
        }

        $departmentSummary = (clone $summaryQuery)
            ->selectRaw("
                COALESCE(NULLIF(employees.department, ''), 'Belum Diisi') as group_label,
                SUM(trainings.hours) as total_hours,
                COUNT(DISTINCT employees.id) as employee_count,
                COUNT(employee_training.training_id) as participation_count
            ")
            ->groupBy('group_label')
            ->orderByDesc('total_hours')
            ->orderBy('group_label')
            ->get()
            ->map(fn ($item) => [
                'label' => $item->group_label,
                'total_hours' => (float) $item->total_hours,
                'employee_count' => (int) $item->employee_count,
                'participation_count' => (int) $item->participation_count,
                'average_hours' => $item->employee_count > 0 ? (float) $item->total_hours / $item->employee_count : 0,
            ]);

        $departmentOptions = $departmentSummary->pluck('label')->values();

        $departmentDetails = (clone $summaryQuery)
            ->selectRaw("
                COALESCE(NULLIF(employees.department, ''), 'Belum Diisi') as department_label,
                trainings.id,
                trainings.name,
                trainings.provider,
                trainings.training_classification,
                trainings.training_sub_classification,
                trainings.start_date,
                trainings.end_date,
                trainings.hours,
                trainings.days,
                COUNT(DISTINCT employees.id) as employee_count
            ")
            ->when($selectedDepartment !== '', fn ($query) => $query->whereRaw(
                "COALESCE(NULLIF(employees.department, ''), 'Belum Diisi') = ?",
                [$selectedDepartment]
            ))
            ->groupBy(
                'department_label',
                'trainings.id',
                'trainings.name',
                'trainings.provider',
                'trainings.training_classification',
                'trainings.training_sub_classification',
                'trainings.start_date',
                'trainings.end_date',
                'trainings.hours',
                'trainings.days'
            )
            ->orderBy('department_label')
            ->orderByDesc('trainings.start_date')
            ->orderByDesc('trainings.id')
            ->get()
            ->groupBy('department_label');

        return view('reports.departments', [
            'selectedYear' => $selectedYear,
            'selectedDepartment' => $selectedDepartment,
            'availableYears' => $this->trainingScope()->select('year')->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year'),
            'departmentOptions' => $departmentOptions,
            'departmentSummary' => $departmentSummary,
            'departmentDetails' => $departmentDetails,
        ]);
    }

    private function trainingScope()
    {
        $query = Training::query();
        $user = Auth::user();

        if ($user?->isDepartmentUser()) {
            $query->where('trainings.department_id', $user->department_id);
        }

        return $query;
    }

    private function employeeScope()
    {
        $query = Employee::query()->where('is_external', false);
        $user = Auth::user();

        if ($user?->isDepartmentUser() && $user->department) {
            $query->where('department', $user->department->name);
        }

        return $query;
    }

    private function applyPeriodFilter($query, int $year, ?int $month = null, ?int $week = null, string $dateColumn = 'start_date')
    {
        $query->whereYear($dateColumn, $year);

        if ($month) {
            $query->whereMonth($dateColumn, $month);
        }

        if ($week) {
            [$weekStart, $weekEnd] = $this->weekRange($year, $week);
            $query->whereBetween($dateColumn, [
                $weekStart->toDateString(),
                $weekEnd->toDateString(),
            ]);
        }

        return $query;
    }

    private function availableYears()
    {
        $yearExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%Y', start_date) as integer)"
            : 'YEAR(start_date)';

        $years = $this->trainingScope()
            ->selectRaw($yearExpression.' as training_year')
            ->whereNotNull('start_date')
            ->distinct()
            ->orderByDesc('training_year')
            ->pluck('training_year')
            ->filter();

        if ($years->isEmpty()) {
            return collect([(int) now()->year]);
        }

        return $years->values();
    }

    private function availableMonths()
    {
        return collect(range(1, 12))->map(fn ($month) => [
            'value' => $month,
            'label' => Carbon::create()->month($month)->translatedFormat('F'),
        ]);
    }

    private function availableWeeks(int $year, ?int $month = null)
    {
        $weeks = collect();

        if ($month) {
            $cursor = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = $cursor->copy()->endOfMonth();

            while ($cursor->lte($endOfMonth)) {
                $week = (int) $cursor->isoWeek();

                if (! $weeks->contains(fn ($item) => $item['value'] === $week)) {
                    [$start, $end] = $this->weekRange($year, $week);
                    $weeks->push([
                        'value' => $week,
                        'label' => 'Week '.$week.' ('.$start->format('d M').' - '.$end->format('d M').')',
                    ]);
                }

                $cursor->addDay();
            }

            return $weeks->values();
        }

        $totalWeeks = (int) Carbon::create($year, 12, 28)->isoWeek();

        return collect(range(1, $totalWeeks))->map(function ($week) use ($year) {
            [$start, $end] = $this->weekRange($year, $week);

            return [
                'value' => $week,
                'label' => 'Week '.$week.' ('.$start->format('d M').' - '.$end->format('d M').')',
            ];
        });
    }

    private function buildTrendSeries($rows, int $year, ?int $month = null, ?int $week = null): array
    {
        if ($week) {
            [$start, $end] = $this->weekRange($year, $week);
            $period = collect();
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                $dateKey = $cursor->toDateString();
                $period->push([
                    'key' => $dateKey,
                    'label' => $cursor->format('D, d M'),
                    'value' => (float) $rows
                        ->filter(fn ($row) => optional($row->start_date)?->toDateString() === $dateKey)
                        ->sum('hours'),
                ]);
                $cursor->addDay();
            }

            return [
                'title' => 'Training Hours by Day',
                'labels' => $period->pluck('label')->all(),
                'values' => $period->pluck('value')->all(),
            ];
        }

        if ($month) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $weekMap = collect();
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                $week = (int) $cursor->isoWeek();

                if (! $weekMap->has($week)) {
                    [$weekStart, $weekEnd] = $this->weekRange($year, $week);
                    $weekMap->put($week, [
                        'label' => 'Week '.$week,
                        'value' => 0,
                        'start' => $weekStart,
                        'end' => $weekEnd,
                    ]);
                }

                $cursor->addDay();
            }

            $period = $weekMap->map(function ($item) use ($rows) {
                $value = (float) $rows
                    ->filter(function ($row) use ($item) {
                        $date = optional($row->start_date);

                        return $date && $date->betweenIncluded($item['start'], $item['end']);
                    })
                    ->sum('hours');

                return [
                    'label' => $item['label'],
                    'value' => $value,
                ];
            })->values();

            return [
                'title' => 'Training Hours by Week',
                'labels' => $period->pluck('label')->all(),
                'values' => $period->pluck('value')->all(),
            ];
        }

        $months = collect(range(1, 12))->map(function ($monthValue) use ($rows) {
            return [
                'label' => Carbon::create()->month($monthValue)->format('M'),
                'value' => (float) $rows
                    ->filter(fn ($row) => optional($row->start_date)?->month === $monthValue)
                    ->sum('hours'),
            ];
        });

        return [
            'title' => 'Training Hours by Month',
            'labels' => $months->pluck('label')->all(),
            'values' => $months->pluck('value')->all(),
        ];
    }

    private function weekRange(int $year, int $week): array
    {
        $start = Carbon::now()->setISODate($year, $week)->startOfWeek(Carbon::MONDAY);

        return [$start, $start->copy()->endOfWeek(Carbon::SUNDAY)];
    }

    private function periodLabel(int $year, ?int $month = null, ?int $week = null): string
    {
        $parts = ['Year '.$year];

        if ($month) {
            $parts[] = Carbon::create()->month($month)->format('F');
        }

        if ($week) {
            [$start, $end] = $this->weekRange($year, $week);
            $parts[] = 'Week '.$week.' ('.$start->format('d M').' - '.$end->format('d M').')';
        }

        return implode(' / ', $parts);
    }

    private function monthOrder(?string $monthLabel): int
    {
        $map = [
            'Jan' => 1,
            'January' => 1,
            '01' => 1,
            'Feb' => 2,
            'February' => 2,
            '02' => 2,
            'Mar' => 3,
            'March' => 3,
            '03' => 3,
            'Apr' => 4,
            'April' => 4,
            '04' => 4,
            'May' => 5,
            '05' => 5,
            'Jun' => 6,
            'June' => 6,
            '06' => 6,
            'Jul' => 7,
            'July' => 7,
            '07' => 7,
            'Aug' => 8,
            'August' => 8,
            '08' => 8,
            'Sep' => 9,
            'Sept' => 9,
            'September' => 9,
            '09' => 9,
            'Oct' => 10,
            'October' => 10,
            '10' => 10,
            'Nov' => 11,
            'November' => 11,
            '11' => 11,
            'Dec' => 12,
            'December' => 12,
            '12' => 12,
        ];

        return $map[$monthLabel] ?? 99;
    }
}
