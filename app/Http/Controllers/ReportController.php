<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\PrintOrder;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\PrintOrdersExport;
use App\Exports\TrainingsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * E. Rapport des Ventes (Filtré par période)
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $report = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_ca'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('date')
            ->get();

        return response()->json(['status' => 'success', 'data' => $report]);
    }

    /**
     * E. Rapport des Impressions (CA par type de support)
     */
    public function printOrdersReport()
    {
        $report = PrintOrder::select(
                'support_type', 
                DB::raw('SUM(total_price) as revenue'), 
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('support_type')
            ->get();

        return response()->json(['status' => 'success', 'data' => $report]);
    }

    /**
     * E. Rapport des Formations (Assiduité & Revenus)
     */
    public function trainingReport()
    {
        $report = TrainingEnrollment::with('training')
            ->select(
                'training_id', 
                DB::raw('COUNT(*) as total_students'), 
                DB::raw('SUM(amount_paid) as total_revenue')
            )
            ->groupBy('training_id')
            ->get();

        return response()->json(['status' => 'success', 'data' => $report]);
    }

    /**
     * Exports Excel
     */
    public function exportSales(): BinaryFileResponse
    {
        return Excel::download(new SalesExport, 'rapport_ventes.xlsx');
    }

    public function exportPrintOrders(): BinaryFileResponse
    {
        return Excel::download(new PrintOrdersExport, 'rapport_impressions.xlsx');
    }

    public function exportTrainings(): BinaryFileResponse
    {
        return Excel::download(new TrainingsExport, 'rapport_formations.xlsx');
    }
}