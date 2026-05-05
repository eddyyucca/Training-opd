<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $department = trim((string) $request->string('department'));
        $status = $request->string('status')->toString();

        $filteredQuery = $this->employeeQuery()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('nik', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('position_title', 'like', "%{$search}%")
                        ->orWhere('whatsapp_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($department !== '', fn ($query) => $query->where('department', $department))
            ->when($status !== '', fn ($query) => $query->where('is_active', $status === 'active'));

        $employees = (clone $filteredQuery)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $statsQuery = clone $filteredQuery;
        $totalEmployees = (clone $statsQuery)->count();
        $activeEmployees = (clone $statsQuery)->where('is_active', true)->count();
        $departmentCount = (clone $statsQuery)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct('department')
            ->count('department');

        return view('employees.index', [
            'employees' => $employees,
            'search' => $search,
            'department' => $department,
            'status' => $status,
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'departmentCount' => $departmentCount,
            'jobLevelGroups' => $this->jobLevelGroups(),
            'departmentOptions' => $this->employeeQuery()
                ->select('department')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->distinct()
                ->orderBy('department')
                ->pluck('department'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return view('employees.create', [
            'employee' => new Employee([
                'is_active' => true,
                'department' => $user?->isDepartmentUser() ? $user->department?->name : null,
            ]),
            'jobLevelGroups' => $this->jobLevelGroups(),
            'genders' => $this->genders(),
            'departments' => Department::query()->orderBy('name')->get(),
            'canChooseDepartment' => $user?->isOpd(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if (Auth::user()?->isDepartmentUser()) {
            $data['department'] = Auth::user()->department?->name;
        }

        Employee::create($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $this->ensureEmployeeAccess($employee);

        $employee->load([
            'attendedTrainings' => fn ($query) => $query
                ->orderByDesc('year')
                ->orderByDesc('start_date')
                ->orderByDesc('id'),
        ]);

        $trainingHistory = $employee->attendedTrainings;

        $classificationSummary = $trainingHistory
            ->groupBy(fn ($training) => $training->training_classification ?: 'Unclassified')
            ->map(fn ($items, $label) => [
                'label' => $label,
                'total' => $items->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(5);

        $monthlyPerformance = $trainingHistory
            ->groupBy(fn ($training) => optional($training->start_date)?->format('Y-m') ?: 'Unknown')
            ->map(function ($items, $period) {
                $date = $items->first()?->start_date;

                return [
                    'label' => $date ? $date->format('M Y') : 'Unknown',
                    'hours' => (float) $items->sum('hours'),
                    'total' => $items->count(),
                    'sort' => $period,
                ];
            })
            ->sortBy('sort')
            ->values();

        return view('employees.show', [
            'employee' => $employee,
            'trainingHistory' => $trainingHistory,
            'totalTraining' => $trainingHistory->count(),
            'totalHours' => (float) $trainingHistory->sum('hours'),
            'latestTrainingDate' => $trainingHistory->first()?->start_date,
            'classificationSummary' => $classificationSummary,
            'monthlyPerformance' => $monthlyPerformance,
            'yearSummary' => $trainingHistory
                ->groupBy(fn ($training) => $training->year ?: 'No Year')
                ->map(fn ($items, $year) => [
                    'year' => $year,
                    'total' => $items->count(),
                    'hours' => (float) $items->sum('hours'),
                ])
                ->sortByDesc('year')
                ->values(),
        ]);
    }

    public function edit(Employee $employee)
    {
        $this->ensureEmployeeAccess($employee);

        return view('employees.edit', [
            'employee' => $employee,
            'jobLevelGroups' => $this->jobLevelGroups(),
            'genders' => $this->genders(),
            'departments' => Department::query()->orderBy('name')->get(),
            'canChooseDepartment' => Auth::user()?->isOpd(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $this->ensureEmployeeAccess($employee);
        $data = $this->validatedData($request, $employee);

        if (Auth::user()?->isDepartmentUser()) {
            $data['department'] = Auth::user()->department?->name;
        }

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->ensureEmployeeAccess($employee);
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    private function validatedData(Request $request, ?Employee $employee = null): array
    {
        $isExternal = $employee?->is_external ?? false;

        return $request->validate([
            'nik' => $isExternal
                ? ['nullable', 'string', 'max:50', Rule::unique('employees', 'nik')->ignore($employee)->whereNull('nik')]
                : ['required', 'string', 'max:50', Rule::unique('employees', 'nik')->ignore($employee)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'division' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'position_title' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'job_level_group' => $isExternal
                ? ['nullable', Rule::in($this->jobLevelGroups())]
                : ['required', Rule::in($this->jobLevelGroups())],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function jobLevelGroups(): array
    {
        return [
            'Senior Management',
            'Manager, Asst Manager, & Supervisor',
            'Staff & Non Staff',
        ];
    }

    private function genders(): array
    {
        return [
            'Male',
            'Female',
        ];
    }

    private function employeeQuery()
    {
        $query = Employee::query();
        $user = Auth::user();

        if ($user?->isDepartmentUser() && $user->department) {
            $query->where('department', $user->department->name);
        }

        return $query;
    }

    private function ensureEmployeeAccess(Employee $employee): void
    {
        $user = Auth::user();

        abort_unless(
            $user?->isOpd() || $employee->department === $user?->department?->name,
            403
        );
    }
}
