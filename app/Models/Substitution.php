<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Substitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'titular_doctor_id',
        'substitute_doctor_id',
        'start_date',
        'end_date',
        'status',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function titularDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'titular_doctor_id');
    }

    public function substituteDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'substitute_doctor_id');
    }
}