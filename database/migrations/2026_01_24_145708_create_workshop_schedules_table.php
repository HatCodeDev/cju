<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workshop_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('location_id')
                ->constrained()
                ->cascadeOnDelete();
            // 1=Lunes, 7=Domingo (ISO-8601 estándar)
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            // Índice compuesto para acelerar la búsqueda de solapamientos:
            // "Busca en este lugar, en este día, a esta hora"
            $table->index(['location_id', 'day_of_week', 'start_time', 'end_time'], 'idx_schedule_overlap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_schedules');
    }
};
