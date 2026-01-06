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
            $table->uuid('uuid')->unique()->index();

            $table->string('full_name');

            $table->string('patient_type')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();

            $table->string('emergency_contact1');
            $table->string('emergency_contact2')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('photo_path')->nullable();

            $table->boolean('is_present')->default(false)->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retirees');
    }
};
