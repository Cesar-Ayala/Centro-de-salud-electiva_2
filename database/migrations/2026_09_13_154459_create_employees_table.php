<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('address', 150)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('town', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('nif', 20)->unique();
            $table->string('social_security_number', 25)->unique();
            
            // Roles administrativos y asistenciales del PDF (cite: 4)
            $table->enum('employee_type', ['ATS', 'ATS_Zona', 'Auxiliar', 'Celador', 'Administrativo']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};