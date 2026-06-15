<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\SalesController; 
use App\Http\Controllers\PrintOrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingEnrollmentController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route par défaut de Laravel
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- NOS ROUTES POUR HORIZON APP ---

// ==========================================
// 1. ROUTES PUBLIQUES (Accessibles sur Navigateur & Thunder Client sans token)
// ==========================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Téléchargement des PDF publics
Route::get('/sales/{id}/invoice', [SalesController::class, 'downloadInvoice']);
Route::get('/print-orders/{id}/pdf', [PrintOrderController::class, 'downloadPdf']);
// AJOUTE LA LIGNE ICI POUR QU'ELLE SOIT PUBLIQUE :
Route::get('/enrollments/{id}/receipt', [TrainingEnrollmentController::class, 'downloadReceipt']); 

Route::get('/enrollments/{id}/certificate', [AttendanceController::class, 'downloadCertificate']);


// ==========================================
// 2. ROUTES PROTÉGÉES (L'utilisateur DOIT être connecté via Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Déconnexion (Module A1)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Module A2 : Tableau de bord (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'getStats']);
    
    // Module B1 : CRUD complet du Catalogue Produits
    Route::apiResource('products', ProductController::class);
    
    // Module B2 : Point de Vente (POS)
    Route::post('/sales', [SalesController::class, 'store']);

    // Module B3 : Gestion des Clients
    Route::get('/customers/{id}/orders', [CustomerController::class, 'orderHistory']);
    Route::apiResource('customers', CustomerController::class);
    
    // Formation
    Route::apiResource('trainings', TrainingController::class);

    // Routes pour les Inscriptions aux Formations
    Route::get('/enrollments', [TrainingEnrollmentController::class, 'index']);
    Route::post('/enrollments', [TrainingEnrollmentController::class, 'store']);
    Route::put('/enrollments/{id}', [TrainingEnrollmentController::class, 'update']);

    // Module C : Gestion des Devis & Impressions Numériques
    Route::get('/print-orders', [PrintOrderController::class, 'index']); 
    Route::post('/print-orders', [PrintOrderController::class, 'store']);
    Route::get('/print-orders/{id}', [PrintOrderController::class, 'show']);
    Route::patch('/print-orders/{id}/status', [PrintOrderController::class, 'updateStatus']);

    // Module D3 : Gestion des Présences
 Route::post('/training-sessions', [AttendanceController::class, 'storeSession']);
 Route::post('/training-sessions/{id}/attendance', [AttendanceController::class, 'submitAttendance']);
 Route::get('/enrollments/{id}/attendance-rate', [AttendanceController::class, 'getStudentAttendanceRate']);
});