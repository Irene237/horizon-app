<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    public function collection() {
        // Retourne les données de la table des ventes
        return Sale::select('id', 'total_amount', 'created_at')->get();
    }

    public function headings(): array {
        return ['ID', 'Montant Total', 'Date de création'];
    }
}