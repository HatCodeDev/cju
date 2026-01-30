<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_service_retiree', function (Blueprint $table) {
            $table->id();

            $table->foreignId('retiree_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('medical_service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_service_retiree');
    }
};
