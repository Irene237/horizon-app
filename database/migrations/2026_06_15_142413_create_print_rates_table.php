<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_rates', function (Blueprint $table) {
            $table->id();
            $table->string('support_type'); // flyer, banderole, t-shirt, kakemono, carte_visite
            $table->string('unit_type'); // m2 (pour banderole/kakemono) ou unit (pour t-shirt/flyer)
            $table->decimal('price', 10, 2); // Le tarif de base en FCFA
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_rates');
    }
};