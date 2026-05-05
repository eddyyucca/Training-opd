<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\TrainingCategory;
use App\Models\TrainingClassification;
use App\Models\TrainingProvider;
use App\Models\TrainingSubClassification;
use App\Models\TrainingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        return view('masters.index', [
            'sections' => $this->sections(),
        ]);
    }

    public function section(string $section): View
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        abort_unless(array_key_exists($section, $this->sections()), 404);

        return view('masters.section', [
            'section' => $section,
            'config' => $this->sections()[$section],
            'items' => $this->itemsForSection($section),
            'classifications' => TrainingClassification::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        [$modelClass, $data] = $this->resolvedPayload($request);

        $modelClass::create($data);

        return back()->with('success', 'Master data added successfully.');
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        $model = $this->resolveModelByType($type, $id);
        [, $data] = $this->resolvedPayload($request, $model, $type);
        $model->update($data);

        return back()->with('success', 'Master data updated successfully.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        $model = $this->resolveModelByType($type, $id);
        $model->delete();

        return back()->with('success', 'Master data deleted successfully.');
    }

    private function resolvedPayload(Request $request, ?Model $model = null, ?string $forcedType = null): array
    {
        $type = $forcedType ?? $request->string('type')->toString();

        return match ($type) {
            'department' => [
                Department::class,
                $request->validate([
                    'code' => ['required', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($model)],
                    'name' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($model)],
                ]),
            ],
            'category' => [
                TrainingCategory::class,
                $request->validate([
                    'name' => ['required', 'string', 'max:255', Rule::unique('training_categories', 'name')->ignore($model)],
                ]),
            ],
            'training_type' => [
                TrainingType::class,
                $request->validate([
                    'name' => ['required', 'string', 'max:255', Rule::unique('training_types', 'name')->ignore($model)],
                ]),
            ],
            'provider' => [
                TrainingProvider::class,
                $request->validate([
                    'name' => ['required', 'string', 'max:255', Rule::unique('training_providers', 'name')->ignore($model)],
                ]),
            ],
            'classification' => [
                TrainingClassification::class,
                $request->validate([
                    'name' => ['required', 'string', 'max:255', Rule::unique('training_classifications', 'name')->ignore($model)],
                ]),
            ],
            'sub_classification' => [
                TrainingSubClassification::class,
                $request->validate([
                    'training_classification_id' => ['required', 'exists:training_classifications,id'],
                    'name' => ['required', 'string', 'max:255'],
                ]),
            ],
            default => abort(404),
        };
    }

    private function resolveModelByType(string $type, int $id): Model
    {
        return match ($type) {
            'department' => Department::findOrFail($id),
            'category' => TrainingCategory::findOrFail($id),
            'training_type' => TrainingType::findOrFail($id),
            'provider' => TrainingProvider::findOrFail($id),
            'classification' => TrainingClassification::findOrFail($id),
            'sub_classification' => TrainingSubClassification::findOrFail($id),
            default => abort(404),
        };
    }

    private function sections(): array
    {
        return [
            'department' => ['title' => 'Departments', 'subtitle' => 'Manage department master data', 'route_type' => 'department'],
            'category' => ['title' => 'Categories', 'subtitle' => 'Manage training categories', 'route_type' => 'category'],
            'training_type' => ['title' => 'Training Types', 'subtitle' => 'Manage training type options', 'route_type' => 'training_type'],
            'provider' => ['title' => 'Providers', 'subtitle' => 'Manage providers and trainers', 'route_type' => 'provider'],
            'classification' => ['title' => 'Classifications', 'subtitle' => 'Manage classifications and sub-classifications', 'route_type' => 'classification'],
        ];
    }

    private function itemsForSection(string $section)
    {
        return match ($section) {
            'department' => Department::query()->orderBy('name')->get(),
            'category' => TrainingCategory::query()->orderBy('name')->get(),
            'training_type' => TrainingType::query()->orderBy('name')->get(),
            'provider' => TrainingProvider::query()->orderBy('name')->get(),
            'classification' => TrainingClassification::query()->with('subClassifications')->orderBy('name')->get(),
            default => collect(),
        };
    }
}
