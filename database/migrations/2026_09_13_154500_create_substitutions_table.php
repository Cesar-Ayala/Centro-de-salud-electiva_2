<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('substitutions', function (Blueprint $table) {
            $table->id();
            
            // Clave Foránea 1: El médico titular que es reemplazado (cite: 4)
            $table->foreignId('titular_doctor_id')->constrained('doctors')->cascadeOnDelete();
            
            // Clave Foránea 2: El médico sustituto que realiza el reemplazo (cite: 4)
            $table->foreignId('substitute_doctor_id')->constrained('doctors')->cascadeOnDelete();
            
            $table->date('start_date'); // Fecha de inicio (cite: 4)
            $table->date('end_date');   // Fecha de fin (cite: 4)
            $table->enum('status', ['Programada', 'Activa', 'Finalizada'])->default('Programada'); // (cite: 4)
            $table->string('reason', 200)->nullable(); // Motivo del reemplazo (cite: 4)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('substitutions');
    }
};