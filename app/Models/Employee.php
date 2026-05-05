<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class Employee extends Model
{
    protected $fillable = [
        'nik',
        'name',
        'gender',
        'division',
        'department',
        'position_title',
        'company',
        'whatsapp_number',
        'email',
        'job_level_group',
        'is_active',
        'is_external',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_external' => 'boolean',
        ];
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class)
            ->withPivot(['registered_at', 'attended_at'])
            ->withTimestamps();
    }

    public function attendedTrainings(): BelongsToMany
    {
        if (! Schema::hasColumn('employee_training', 'attended_at')) {
            return $this->trainings();
        }

        return $this->trainings()->wherePivotNotNull('attended_at');
    }
}
