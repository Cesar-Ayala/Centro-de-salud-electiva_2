<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula la cuenta de usuario con su ficha real:
     * un usuario con rol "doctor" apunta a un registro de doctors,
     * y uno con rol "patient" apunta a un registro de patients.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('role')->constrained('doctors')->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->after('doctor_id')->constrained('patients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['doctor_id', 'patient_id']);
        });
    }
};
