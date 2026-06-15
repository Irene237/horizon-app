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
    Schema::create('training_enrollments', function (Blueprint $table) {
        $table->id();
        // Liaison avec le client (apprenant)
        $table->foreignId('customer_id')->constrained()->onDelete('cascade');
        // Liaison avec la formation
        $table->foreignId('training_id')->constrained()->onDelete('cascade');
        
        // Suivi financier de l'inscription
        $table->enum('payment_status', ['payé', 'partiel', 'non payé'])->default('non payé');
        $table->decimal('amount_paid', 10, 2)->default(0.00); // Somme déjà versée
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_enrollments');
    }
};
