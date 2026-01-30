<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retirees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // CRÍTICO: Para tu escáner QR

            // Datos Generales
            $table->string('full_name');
            $table->string('curp', 18)->unique();
            $table->string('phone', 20);
            $table->date('birth_date');
            $table->string('gender'); // Mantenemos género para tu lógica actual

            // Foto
            $table->string('photo_path')->nullable(); // CRÍTICO: Para la credencial

            // Estado de Asistencia
            $table->boolean('is_present')->default(false); // CRÍTICO: Para ScanStation

            // Lógica de Negocio (Nuevos campos normalizados)
            $table->string('retiree_type'); // Antes patient_type
            $table->string('worker_id')->nullable();

            // Datos Salud
            $table->text('allergies')->nullable();
            $table->string('insurance_type');
            $table->string('insurance_name')->nullable();

            // Notas
            $table->text('other_services_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retirees');
    }
};
