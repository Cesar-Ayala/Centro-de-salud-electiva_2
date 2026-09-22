<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'postal_code',
        'nif',
        'social_security_number',
        'doctor_id',
    ];

    /** Un paciente pertenece a un medico asignado (N:1). */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
