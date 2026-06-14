<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // On nomme la fonction getStats pour correspondre exactement à ta route !
    public function getStats() 
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'ventes_du_jour' => 12, 
                'chiffre_affaires_mois' => 450000, 
                'stock_critique' => 5, 
                'commandes_impression_attente' => 3, 
                'apprenants_inscrits_ce_mois' => 8, 
                'graphique_ventes_30_jours' => [
                    'labels' => ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
                    'datasets' => [25, 40, 35, 50] 
                ]
            ]
        ], 200);
    }
}