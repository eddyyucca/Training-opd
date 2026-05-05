<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingScanController extends Controller
{
    public function showRegistrationSuccess(Request $request, string $token): View|RedirectResponse
    {
        $training = $this->findTrainingByToken('registration_token', $token);

        return $this->showResultPage($request, $training, 'registration');
    }

    public function showAttendanceSuccess(Request $request, string $token): View|RedirectResponse
    {
        $training = $this->findTrainingByToken('attendance_token', $token);

        return $this->showResultPage($request, $training, 'attendance');
    }

    public function showRegistration(string $token): View
    {
        $training = $this->findTrainingByToken('registration_token', $token);
        abort_unless($training->registrationIsOpen() && $training->registrationHasSpace(), 404);

        return $this->showPage($training, 'registration');
    }

    public function submitRegistration(Request $request, string $token): RedirectResponse
    {
        $training = $this->findTrainingByToken('registration_token', $token);
        abort_unless($training->registrationIsOpen(), 404);

        if (! $training->registrationHasSpace()) {
            return back()->withErrors(['employee_id' => 'Registration quota is full.']);
        }

        $mode = $request->input('register_mode', 'existing');

        if ($mode === 'external') {
            return $this->handleExternalRegistration($request, $training, $token);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
        $employeeId = $validated['employee_id'];

        $existing = $training->employees()->where('employees.id', $employeeId)->first();

        Employee::query()->whereKey($employeeId)->update([
            'whatsapp_number' => $validated['whatsapp_number'],
            'email' => $validated['email'] ?: null,
        ]);

        if ($existing && $existing->pivot->registered_at) {
            return redirect()->route('trainings.registration.success', $token)->with('scan_result', [
                'employee_id' => $employeeId,
                'status' => 'already_registered',
            ]);
        }

        $training->employees()->syncWithoutDetaching([
            $employeeId => [
                'registered_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        return redirect()->route('trainings.registration.success', $token)->with('scan_result', [
            'employee_id' => $employeeId,
            'status' => 'registered',
        ]);
    }

    public function showAttendance(string $token): View
    {
        $training = $this->findTrainingByToken('attendance_token', $token);
        abort_unless($training->attendanceIsOpen(), 404);

        return $this->showPage($training, 'attendance');
    }

    public function submitAttendance(Request $request, string $token): RedirectResponse
    {
        $training = $this->findTrainingByToken('attendance_token', $token);
        abort_unless($training->attendanceIsOpen(), 404);

        $mode = $request->input('register_mode', 'existing');

        if ($mode === 'external') {
            return $this->handleExternalAttendance($request, $training, $token);
        }

        $employeeId = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ])['employee_id'];

        $registeredEmployee = $training->registeredEmployees()
            ->where('employees.id', $employeeId)
            ->first();

        if (! $registeredEmployee) {
            return back()->withErrors(['employee_id' => 'This employee is not registered for this training.']);
        }

        $existingAttendance = $training->attendedEmployees()
            ->where('employees.id', $employeeId)
            ->first();

        $training->employees()->syncWithoutDetaching([
            $employeeId => [
                'registered_at' => $registeredEmployee->pivot->registered_at ?? now(),
                'attended_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $training->employees()->updateExistingPivot($employeeId, [
            'registered_at' => $registeredEmployee->pivot->registered_at ?? now(),
            'attended_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('trainings.attendance.success', $token)->with('scan_result', [
            'employee_id' => $employeeId,
            'status' => $existingAttendance ? 'already_attended' : 'attended',
        ]);
    }

    private function handleExternalRegistration(Request $request, Training $training, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'ext_name' => ['required', 'string', 'max:255'],
            'ext_company' => ['nullable', 'string', 'max:255'],
            'ext_position' => ['nullable', 'string', 'max:255'],
            'ext_gender' => ['nullable', 'in:Male,Female'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $employee = Employee::create([
            'name' => $validated['ext_name'],
            'company' => $validated['ext_company'] ?? null,
            'position_title' => $validated['ext_position'] ?? null,
            'gender' => $validated['ext_gender'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'],
            'email' => $validated['email'] ?? null,
            'is_external' => true,
            'is_active' => true,
        ]);

        $training->employees()->syncWithoutDetaching([
            $employee->id => [
                'registered_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        return redirect()->route('trainings.registration.success', $token)->with('scan_result', [
            'employee_id' => $employee->id,
            'status' => 'registered',
        ]);
    }

    private function handleExternalAttendance(Request $request, Training $training, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'ext_name' => ['required', 'string', 'max:255'],
            'ext_company' => ['nullable', 'string', 'max:255'],
            'ext_position' => ['nullable', 'string', 'max:255'],
            'ext_gender' => ['nullable', 'in:Male,Female'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $employee = Employee::create([
            'name' => $validated['ext_name'],
            'company' => $validated['ext_company'] ?? null,
            'position_title' => $validated['ext_position'] ?? null,
            'gender' => $validated['ext_gender'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'],
            'email' => $validated['email'] ?? null,
            'is_external' => true,
            'is_active' => true,
        ]);

        $training->employees()->syncWithoutDetaching([
            $employee->id => [
                'registered_at' => now(),
                'attended_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        return redirect()->route('trainings.attendance.success', $token)->with('scan_result', [
            'employee_id' => $employee->id,
            'status' => 'attended',
        ]);
    }

    private function showPage(Training $training, string $mode): View
    {
        if ($mode === 'attendance') {
            $employees = $training->registeredEmployees()
                ->where('employees.is_active', true)
                ->orderBy('employees.name')
                ->get();

            return view('trainings.scan', [
                'training' => $training->loadCount(['registeredEmployees', 'attendedEmployees']),
                'mode' => $mode,
                'employees' => $employees,
            ]);
        }

        $employeeQuery = Employee::query()->where('is_active', true)->where('is_external', false);

        return view('trainings.scan', [
            'training' => $training->loadCount(['registeredEmployees', 'attendedEmployees']),
            'mode' => $mode,
            'employees' => $employeeQuery->orderBy('name')->get(),
        ]);
    }

    private function findTrainingByToken(string $column, string $token): Training
    {
        return Training::query()->where($column, $token)->firstOrFail();
    }

    private function showResultPage(Request $request, Training $training, string $mode): View|RedirectResponse
    {
        $result = $request->session()->get('scan_result');

        if (! is_array($result) || empty($result['employee_id'])) {
            return redirect()->route(
                $mode === 'registration' ? 'trainings.registration.show' : 'trainings.attendance.show',
                $mode === 'registration' ? $training->registration_token : $training->attendance_token
            );
        }

        $employee = Employee::query()->findOrFail($result['employee_id']);
        $pivot = $training->employees()->where('employees.id', $employee->id)->first()?->pivot;

        return view('trainings.scan-result', [
            'training' => $training->load('department'),
            'employee' => $employee,
            'mode' => $mode,
            'status' => $result['status'] ?? null,
            'registeredAt' => $pivot?->registered_at,
            'attendedAt' => $pivot?->attended_at,
        ]);
    }
}
