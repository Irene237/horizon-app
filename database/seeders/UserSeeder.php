<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Compte Super-Administrateur (Accès total)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@horizon.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Compte Vendeur (Accès vente + stock)
        User::create([
            'name' => 'Jean Vendeur',
            'email' => 'vendeur@horizon.com',
            'password' => Hash::make('password123'),
            'role' => 'vendeur',
        ]);

        // 3. Compte Agent Impression (Accès impression uniquement)
        User::create([
            'name' => 'Paul Impression',
            'email' => 'print@horizon.com',
            'password' => Hash::make('password123'),
            'role' => 'print_agent',
        ]);

        // 4. Compte Formateur (Accès formation uniquement)
        User::create([
            'name' => 'Marie Formatrice',
            'email' => 'formateur@horizon.com',
            'password' => Hash::make('password123'),
            'role' => 'formateur',
        ]);
    }
}