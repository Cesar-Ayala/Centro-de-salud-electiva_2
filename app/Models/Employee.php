<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
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
        'employee_type',
    ];

    /** Un empleado tiene muchos periodos de vacaciones (1:N). */
    public function vacations(): HasMany
    {
        return $this->hasMany(EmployeeVacation::class);
    }
}
