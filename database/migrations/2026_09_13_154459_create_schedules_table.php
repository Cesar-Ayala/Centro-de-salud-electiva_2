<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // FK: Horario pertenece a un médico (cite: 4)
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            
            $table->enum('day_of_week', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']);
            $table->time('start_time'); // Hora de inicio de consulta (cite: 4)
            $table->time('end_time');   // Hora de fin de consulta (cite: 4)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};