<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\SalesController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route par défaut de Laravel
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- NOS ROUTES POUR HORIZON APP ---

// 1. Routes publiques : accessibles sans être connecté
Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

// Route POS - Téléchargement Facture PDF (Placée ici temporairement pour le test sur Navigateur)
Route::get('/sales/{id}/invoice', [SalesController::class, 'downloadInvoice']);


// 2. Routes protégées : l'utilisateur DOIT avoir un Token valide pour y accéder
Route::middleware('auth:sanctum')->group(function () {
    
    // Déconnexion (Module A1)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Module A2 : Tableau de bord (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'getStats']);
    
    // Module B1 : CRUD complet du Catalogue Produits
    Route::apiResource('products', ProductController::class); // : Gère index, store, show, update, destroy automatiquement !
    
    // Module B2 : Point de Vente (POS)
    Route::post('/sales', [SalesController::class, 'store']);

    // Module B3 : Gestion des Clients
    Route::get('/customers/{id}/orders', [App\Http\Controllers\CustomerController::class, 'orderHistory']);
    Route::apiResource('customers', App\Http\Controllers\CustomerController::class);
});