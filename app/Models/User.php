<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Roles definidos en el documento del proyecto. */
    public const ROLES = ['admin', 'operator', 'doctor', 'patient'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'doctor_id',
        'patient_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Ficha de medico vinculada a la cuenta (solo rol doctor). */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** Ficha de paciente vinculada a la cuenta (solo rol patient). */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /** Verifica si el usuario tiene alguno de los roles indicados. */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Etiqueta legible del rol para la interfaz. */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrador',
            'operator' => 'Operador',
            'doctor' => 'Médico',
            'patient' => 'Paciente',
            default => $this->role,
        };
    }
}
