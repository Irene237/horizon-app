<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route par défaut de Laravel
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
    
    // Module A2 : Tableau de bord (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'getStats']);
    
    // Module B1 : CRUD complet du Catalogue Produits
    Route::apiResource('products', ProductController::class); // <-- AJOUTÉ : Gère index, store, show, update, destroy automatiquement !
    
    // C'est ici qu'on ajoutera plus tard les routes pour les ventes, les impressions, etc.
});