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
        // On appelle notre UserSeeder pour injecter les 4 rôles d'Horizon Numérique
        $this->call([
            UserSeeder::class,
        ]);
    }
}