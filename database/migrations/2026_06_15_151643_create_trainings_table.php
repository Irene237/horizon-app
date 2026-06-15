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
    Schema::create('trainings', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('duration_hours'); // Durée en heures
        $table->enum('level', ['débutant', 'intermédiaire', 'avancé'])->default('débutant');
        $table->string('trainer_name'); // Nom du formateur assigné
        $table->decimal('price', 10, 2); // Prix de la formation (ex: 75000.00 FCFA)
        $table->integer('max_capacity'); // Nombre max de places disponibles
        $table->date('start_date');
        $table->date('end_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
