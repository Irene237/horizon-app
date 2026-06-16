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
    Schema::create('attendance_sheets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('training_session_id')->constrained()->onDelete('cascade');
        // Liaison directe avec l'inscription pour faciliter les calculs
        $table->foreignId('training_enrollment_id')->constrained()->onDelete('cascade');
        $table->enum('status', ['présent', 'absent', 'retard'])->default('présent');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sheets');
    }
};
