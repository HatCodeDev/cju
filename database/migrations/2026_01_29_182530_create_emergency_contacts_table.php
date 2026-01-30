<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();

            // Relación 1:N (Un jubilado tiene contactos)
            $table->foreignId('retiree_id')
                ->constrained()
                ->cascadeOnDelete(); // Si se borra el jubilado, se borran sus contactos

            $table->string('name');
            $table->string('phone', 20);
            $table->string('relationship'); // Parentesco

            // Datos laborales del contacto
            $table->boolean('is_university_worker')->default(false);
            $table->string('dependency')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
