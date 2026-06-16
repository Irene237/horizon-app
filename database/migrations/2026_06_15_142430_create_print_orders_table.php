<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null'); // Client demandeur
            $table->string('support_type'); // flyer, banderole, etc.
            $table->decimal('width', 8, 2)->nullable(); // Largeur en cm
            $table->decimal('height', 8, 2)->nullable(); // Hauteur en cm
            $table->integer('quantity'); // Quantité demandée
            $table->string('file_path')->nullable(); // Upload du fichier (PDF, PNG, JPG)
            $table->decimal('unit_price', 10, 2); // Prix unitaire appliqué
            $table->decimal('total_price', 10, 2); // Total calculé automatiquement
            
            // Statuts : quote (devis), pending (en attente), processing (en production), ready (prêt), delivered (livré)
            $table->string('status')->default('quote'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_orders');
    }
};