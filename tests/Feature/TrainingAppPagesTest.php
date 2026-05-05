<?php

namespace Tests\Feature;

use App\Models\Department;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrainingAppPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function actingAsOpdUser(): User
    {
        $department = Department::query()->where('code', 'OPD')->firstOrFail();

        $user = User::factory()->create([
            'role' => 'opd',
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_dashboard_page_can_be_rendered(): void
    {
        $this->actingAsOpdUser();
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_employee_pages_can_be_rendered(): void
    {
        $department = Department::query()->where('code', 'OPD')->firstOrFail();

        $user = User::factory()->create([
            'role' => 'opd',
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $employee = Employee::create([
            'nik' => 'EMP001',
            'name' => 'Test Employee',
            'department' => $department->name,
        ]);

        $this->get(route('employees.index'))->assertOk();
        $this->get(route('employees.create'))->assertOk();
        $this->get(route('employees.show', $employee))->assertOk();
        $this->get(route('employees.edit', $employee))->assertOk();
    }

    public function test_training_pages_can_be_rendered(): void
    {
        $department = Department::query()->where('code', 'OPD')->firstOrFail();

        $user = User::factory()->create([
            'role' => 'opd',
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $training = Training::create([
            'year' => 2026,
            'name' => 'Safety Induction',
            'hours' => 8,
            'days' => 1,
            'department_id' => $department->id,
        ]);

        $this->get(route('trainings.index'))->assertOk();
        $this->get(route('trainings.create'))->assertOk();
        $this->get(route('trainings.show', $training))->assertOk();
        $this->get(route('trainings.edit', $training))->assertOk();
        $this->get(route('trainings.registration.show', $training->registration_token))->assertOk();
        $this->get(route('trainings.attendance.show', $training->attendance_token))->assertOk();
    }

    public function test_training_attendance_is_recorded_from_scan(): void
    {
        $department = Department::query()->where('code', 'OHS')->firstOrFail();

        $employee = Employee::create([
            'nik' => 'EMP001',
            'name' => 'Test Employee',
            'job_level_group' => 'Staff & Non Staff',
            'is_active' => true,
            'department' => $department->name,
        ]);

        $training = Training::create([
            'year' => 2026,
            'name' => 'Safety Induction',
            'hours' => 8,
            'days' => 1,
            'department_id' => $department->id,
        ]);

        $this->post(route('trainings.attendance.submit', $training->attendance_token), [
            'employee_id' => $employee->id,
        ])->assertSessionHasErrors();

        $this->post(route('trainings.registration.submit', $training->registration_token), [
            'employee_id' => $employee->id,
            'whatsapp_number' => '08123456789',
            'email' => 'employee@example.com',
        ])->assertRedirect();

        $this->post(route('trainings.attendance.submit', $training->attendance_token), [
            'employee_id' => $employee->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('employee_training', [
            'employee_id' => $employee->id,
            'training_id' => $training->id,
        ]);

        $this->actingAs(User::factory()->create([
            'role' => 'opd',
            'department_id' => Department::query()->where('code', 'OPD')->firstOrFail()->id,
        ]));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1');
    }
}
