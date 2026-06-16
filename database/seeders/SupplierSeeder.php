<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        // On crée un premier fournisseur de test
        Supplier::create([
            'name' => 'Horizon Fournisseur Général',
            'phone' => '+237600000000'
        ]);
    }
}
