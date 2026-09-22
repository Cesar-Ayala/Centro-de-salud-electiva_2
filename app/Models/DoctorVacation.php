<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorVacation extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorVacationFactory> */
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'start_date',
        'end_date',
        'status',
        'reason',
        'observations',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public const STATUSES = ['Planificadas', 'Disfrutadas', 'Canceladas'];

    /** Un periodo de vacaciones pertenece a un medico (N:1). */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
