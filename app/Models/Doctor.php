<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'town',
        'province',
        'postal_code',
        'nif',
        'social_security_number',
        'collegiate_number',
        'doctor_type',
        'hiring_date',
        'discharge_date',
    ];

    protected $casts = [
        'hiring_date' => 'date',
        'discharge_date' => 'date',
    ];

    /** Un médico atiende muchos pacientes (1:N). */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /** Un médico tiene muchos horarios de consulta (1:N). */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /** Un médico tiene muchos periodos de vacaciones (1:N). */
    public function vacations(): HasMany
    {
        return $this->hasMany(DoctorVacation::class);
    }

    /** Sustituciones en las que este médico es el titular ausente. */
    public function substitutionsAsTitular(): HasMany
    {
        return $this->hasMany(Substitution::class, 'titular_doctor_id');
    }

    /** Sustituciones en las que este médico actúa como sustituto. */
    public function substitutionsAsSubstitute(): HasMany
    {
        return $this->hasMany(Substitution::class, 'substitute_doctor_id');
    }

    /** Usuarios del sistema vinculados a este médico. */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** El médico está activo mientras no tenga fecha de baja. */
    public function getIsActiveAttribute(): bool
    {
        return is_null($this->discharge_date);
    }
}