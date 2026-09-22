<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('address', 150)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('nif', 20)->unique();
            $table->string('social_security_number', 25)->unique();
            
            // Clave Foránea: Relación 1:N entre Médico y Paciente (cite: 1, 4)
            // Un paciente tiene asignado un médico (doctor_id) (cite: 4)
            $table->foreignId('doctor_id')
                  ->nullable()
                  ->constrained('doctors')
                  ->nullOnDelete();
                  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};