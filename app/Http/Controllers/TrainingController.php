<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Training;
use App\Models\TrainingCategory;
use App\Models\TrainingClassification;
use App\Models\TrainingProvider;
use App\Models\TrainingSubClassification;
use App\Models\TrainingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $year = $request->integer('year');

        $trainings = $this->trainingQuery()
            ->with('department')
            ->withCount([
                'registeredEmployees',
                'attendedEmployees',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('provider', 'like', "%{$search}%")
                        ->orWhere('training_classification', 'like', "%{$search}%")
                        ->orWhere('training_sub_classification', 'like', "%{$search}%");
                });
            })
            ->when($year, fn ($query) => $query->where('year', $year))
            ->orderByDesc('year')
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('trainings.index', [
            'trainings' => $trainings,
            'search' => $search,
            'year' => $year,
            'years' => $this->yearOptions(),
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return view('trainings.create', $this->formData(new Training([
            'department_id' => $user?->isDepartmentUser() ? $user->department_id : null,
            'month' => now()->translatedFormat('F'),
            'year' => now()->year,
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data = $this->prepareTrainingPayload($data);

        if (Auth::user()?->isDepartmentUser()) {
            $data['department_id'] = Auth::user()->department_id;
        }

        $training = Training::create($data);

        return redirect()
            ->route('trainings.show', $training)
            ->with('success', 'Training added successfully.');
    }

    public function edit(Training $training)
    {
        $this->ensureTrainingAccess($training);

        return view('trainings.edit', $this->formData($training));
    }

    public function show(Training $training)
    {
        $this->ensureTrainingAccess($training);
        $training->load([
            'department',
            'registeredEmployees' => fn ($query) => $query->orderBy('name'),
            'attendedEmployees' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('trainings.show', [
            'training' => $training,
        ]);
    }

    public function registrationQr(Training $training)
    {
        $this->ensureTrainingAccess($training);

        return view('trainings.qr', [
            'training' => $training->load('department'),
            'mode' => 'registration',
            'publicUrl' => $training->registrationIsOpen()
                ? route('trainings.registration.show', $training->registration_token)
                : null,
        ]);
    }

    public function attendanceQr(Training $training)
    {
        $this->ensureTrainingAccess($training);

        return view('trainings.qr', [
            'training' => $training->load('department'),
            'mode' => 'attendance',
            'publicUrl' => $training->attendanceIsOpen()
                ? route('trainings.attendance.show', $training->attendance_token)
                : null,
        ]);
    }

    public function update(Request $request, Training $training)
    {
        $this->ensureTrainingAccess($training);

        $data = $this->validatedData($request);
        $data = $this->prepareTrainingPayload($data);

        if (Auth::user()?->isDepartmentUser()) {
            $data['department_id'] = Auth::user()->department_id;
        }

        $training->update($data);

        return redirect()
            ->route('trainings.show', $training)
            ->with('success', 'Training updated successfully.');
    }

    public function destroy(Training $training)
    {
        $this->ensureTrainingAccess($training);
        $training->delete();

        return redirect()
            ->route('trainings.index')
            ->with('success', 'Training deleted successfully.');
    }

    private function formData(Training $training): array
    {
        return [
            'training' => $training,
            'years' => $this->yearOptions(),
            'months' => $this->monthOptions(),
            'departments' => Department::query()->orderBy('name')->get(),
            'categories' => TrainingCategory::query()->orderBy('name')->get(),
            'types' => TrainingType::query()->orderBy('name')->get(),
            'providers' => TrainingProvider::query()->orderBy('name')->get(),
            'classifications' => TrainingClassification::query()
                ->with('subClassifications')
                ->orderBy('name')
                ->get(),
            'canChooseDepartment' => Auth::user()?->isOpd(),
        ];
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'training_classification_id' => ['nullable', 'exists:training_classifications,id'],
            'training_sub_classification_id' => ['nullable', 'exists:training_sub_classifications,id'],
            'training_category_id' => ['nullable', 'exists:training_categories,id'],
            'training_type_id' => ['nullable', 'exists:training_types,id'],
            'training_provider_id' => ['nullable', 'exists:training_providers,id'],
            'month' => ['nullable', 'string', 'max:20'],
            'start_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'cost_per_person' => ['nullable', 'numeric', 'min:0'],
            'pr_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function prepareTrainingPayload(array $data): array
    {
        $classification = isset($data['training_classification_id'])
            ? TrainingClassification::find($data['training_classification_id'])
            : null;
        $subClassification = isset($data['training_sub_classification_id'])
            ? TrainingSubClassification::find($data['training_sub_classification_id'])
            : null;
        $category = isset($data['training_category_id'])
            ? TrainingCategory::find($data['training_category_id'])
            : null;
        $type = isset($data['training_type_id'])
            ? TrainingType::find($data['training_type_id'])
            : null;
        $provider = isset($data['training_provider_id'])
            ? TrainingProvider::find($data['training_provider_id'])
            : null;

        $data['training_classification'] = $classification?->name;
        $data['training_sub_classification'] = $subClassification?->name;
        $data['category'] = $category?->name;
        $data['training_type'] = $type?->name;
        $data['provider'] = $provider?->name;
        [$data['hours'], $data['days']] = $this->calculateDuration(
            $data['start_date'] ?? null,
            $data['start_time'] ?? null,
            $data['end_date'] ?? null,
            $data['end_time'] ?? null,
        );

        return $data;
    }

    private function calculateDuration(?string $startDate, ?string $startTime, ?string $endDate, ?string $endTime): array
    {
        if (! $startDate) {
            return [0, 0];
        }

        $resolvedEndDate = $endDate ?: $startDate;
        $resolvedStartTime = $startTime ?: '00:00';
        $resolvedEndTime = $endTime ?: $resolvedStartTime;

        $start = Carbon::parse($startDate.' '.$resolvedStartTime);
        $end = Carbon::parse($resolvedEndDate.' '.$resolvedEndTime);

        if ($end->lt($start)) {
            $end = $start;
        }

        $days = $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1;
        $dailyStart = Carbon::parse('2000-01-01 '.$resolvedStartTime);
        $dailyEnd = Carbon::parse('2000-01-01 '.$resolvedEndTime);
        $dailyHours = round(max(0, ($dailyEnd->getTimestamp() - $dailyStart->getTimestamp()) / 3600), 2);
        $hours = round($dailyHours * max($days, 1), 2);

        return [$hours, $days];
    }

    private function trainingQuery(): Builder
    {
        $query = Training::query();
        $user = Auth::user();

        if ($user?->isDepartmentUser()) {
            $query->where('department_id', $user->department_id);
        }

        return $query;
    }

    private function ensureTrainingAccess(Training $training): void
    {
        $user = Auth::user();

        abort_unless(
            $user?->isOpd() || $training->department_id === $user?->department_id,
            403
        );
    }

    private function yearOptions(): array
    {
        $currentYear = (int) now()->format('Y');
        $range = range($currentYear - 5, $currentYear + 5);
        $existing = Training::query()
            ->select('year')
            ->whereNotNull('year')
            ->distinct()
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->all();

        $years = array_unique([...$range, ...$existing]);
        rsort($years);

        return $years;
    }

    private function monthOptions(): array
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];
    }
}
