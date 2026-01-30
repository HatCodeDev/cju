<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retiree_workshop', function (Blueprint $table) {
            $table->id();

            $table->foreignId('retiree_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('workshop_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retiree_workshop');
    }
};
