<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id(); // Identificador único (Primary Key) (cite: 1)
            $table->string('name', 100); // Nombre completo (cite: 4)
            $table->string('address', 150)->nullable(); // Dirección (opcional) (cite: 4)
            $table->string('phone', 15)->nullable(); // Teléfono (cite: 4)
            $table->string('town', 100)->nullable(); // Población (cite: 4)
            $table->string('province', 100)->nullable(); // Provincia (cite: 4)
            $table->string('postal_code', 10)->nullable(); // Código Postal (cite: 4)
            
            // Atributos únicos: no pueden repetirse entre médicos (cite: 4)
            $table->string('nif', 20)->unique(); // NIF / Cédula (cite: 4)
            $table->string('social_security_number', 25)->unique(); // Num. Seguridad Social (cite: 4)
            $table->string('collegiate_number', 20)->unique(); // Número de Colegiado (cite: 4)
            
            // Tipo de médico según los valores indicados en el PDF (cite: 4)
            $table->enum('doctor_type', ['Titular', 'Interino', 'Sustituto']);
            
            // Fechas para gestionar ingresos y bajas de sustitutos o personal (cite: 4)
            $table->date('hiring_date')->nullable();    // fecha_alta (cite: 4)
            $table->date('discharge_date')->nullable(); // fecha_baja (cite: 4)
            
            $table->timestamps(); // Crea automáticamente created_at y updated_at (cite: 1)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};