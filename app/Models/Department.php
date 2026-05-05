<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
