<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // On appelle nos seeders pour injecter les utilisateurs et le fournisseur par défaut
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class, 
        ]);
    }
}