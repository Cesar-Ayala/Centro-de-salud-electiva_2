<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeVacation extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeVacationFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
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

    /** Un periodo de vacaciones pertenece a un empleado (N:1). */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
