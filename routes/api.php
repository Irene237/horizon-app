<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route par défaut de Laravel (tu peux la laisser)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- NOS ROUTES POUR HORIZON APP ---

// 1. Route publique : accessible sans être connecté (pour pouvoir soumettre ses identifiants)
Route::post('/login', [AuthController::class, 'login']);

// 2. Routes protégées : l'utilisateur DOIT avoir un Token valide pour y accéder
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // C'est ici qu'on ajoutera plus tard les routes pour les ventes, les impressions, etc.
});