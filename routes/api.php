<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController; // 1. AJOUTE CETTE LIGNE ICI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route par défaut de Laravel (tu peux la laisser)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- NOS ROUTES POUR HORIZON APP ---

// 1. Route publique : accessible sans être connecté
Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

// 2. Routes protégées : l'utilisateur DOIT avoir un Token valide pour y accéder
Route::middleware('auth:sanctum')->group(function () {
    
    // Déconnexion (Module A1)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 2. AJOUTE CETTE LIGNE ICI POUR LE MODULE A2 (Dashboard)
   Route::get('/dashboard', [DashboardController::class, 'getStats']);
    // C'est ici qu'on ajoutera plus tard les routes pour les ventes, les impressions, etc.
});