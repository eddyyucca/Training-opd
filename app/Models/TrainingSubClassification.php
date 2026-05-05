<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingSubClassification extends Model
{
    protected $fillable = [
        'training_classification_id',
        'name',
    ];

    public function classification(): BelongsTo
    {
        return $this->belongsTo(TrainingClassification::class, 'training_classification_id');
    }
}
