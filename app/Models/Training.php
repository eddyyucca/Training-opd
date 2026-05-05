<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Training extends Model
{
    protected $fillable = [
        'year',
        'name',
        'training_classification',
        'training_sub_classification',
        'category',
        'training_type',
        'provider',
        'month',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'hours',
        'days',
        'quota',
        'cost_per_person',
        'pr_number',
        'notes',
        'registration_token',
        'attendance_token',
        'department_id',
        'training_category_id',
        'training_type_id',
        'training_provider_id',
        'training_classification_id',
        'training_sub_classification_id',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'hours' => 'decimal:2',
            'days' => 'integer',
            'quota' => 'integer',
            'cost_per_person' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Training $training) {
            $training->registration_token ??= (string) Str::uuid();
            $training->attendance_token ??= (string) Str::uuid();
        });
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot(['registered_at', 'attended_at'])
            ->withTimestamps();
    }

    public function registeredEmployees(): BelongsToMany
    {
        if (! Schema::hasColumn('employee_training', 'registered_at')) {
            return $this->employees();
        }

        return $this->employees()->wherePivotNotNull('registered_at');
    }

    public function attendedEmployees(): BelongsToMany
    {
        if (! Schema::hasColumn('employee_training', 'attended_at')) {
            return $this->employees();
        }

        return $this->employees()->wherePivotNotNull('attended_at');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function categoryMaster(): BelongsTo
    {
        return $this->belongsTo(TrainingCategory::class, 'training_category_id');
    }

    public function typeMaster(): BelongsTo
    {
        return $this->belongsTo(TrainingType::class, 'training_type_id');
    }

    public function providerMaster(): BelongsTo
    {
        return $this->belongsTo(TrainingProvider::class, 'training_provider_id');
    }

    public function classificationMaster(): BelongsTo
    {
        return $this->belongsTo(TrainingClassification::class, 'training_classification_id');
    }

    public function subClassificationMaster(): BelongsTo
    {
        return $this->belongsTo(TrainingSubClassification::class, 'training_sub_classification_id');
    }

    public function startsAt(): ?Carbon
    {
        if (! $this->start_date) {
            return null;
        }

        $time = $this->start_time?->format('H:i:s') ?? '00:00:00';

        return Carbon::parse($this->start_date->format('Y-m-d').' '.$time);
    }

    public function endsAt(): ?Carbon
    {
        $date = $this->end_date ?? $this->start_date;

        if (! $date) {
            return null;
        }

        $time = $this->end_time?->format('H:i:s') ?? '23:59:59';

        return Carbon::parse($date->format('Y-m-d').' '.$time);
    }

    public function registrationIsOpen(): bool
    {
        $startsAt = $this->startsAt();

        return $startsAt ? now()->lt($startsAt) : true;
    }

    public function attendanceIsOpen(): bool
    {
        $endsAt = $this->endsAt();

        return $endsAt ? now()->lte($endsAt) : true;
    }

    public function registrationHasSpace(): bool
    {
        if (! $this->quota) {
            return true;
        }

        return $this->registeredEmployees()->count() < $this->quota;
    }
}
