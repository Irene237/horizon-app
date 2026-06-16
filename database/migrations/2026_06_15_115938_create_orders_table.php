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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        // foreignIdId facultative si vente anonyme, donc on met nullable()
        $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le vendeur qui fait la vente
        
        $table->decimal('subtotal', 10, 2); // Montant avant remise
        $table->decimal('discount', 10, 2)->default(0.00); // Montant de la remise appliquée
        $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
        $table->decimal('total', 10, 2); // Montant final à payer
        
        $table->enum('payment_mode', ['cash', 'mobile_money', 'transfer']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
