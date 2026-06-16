<?php

namespace App\Exports;

use App\Models\PrintOrder; // <-- Importation du BON modèle
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrintOrdersExport implements FromCollection, WithHeadings
{
    public function collection() {
        return PrintOrder::select('id', 'support_type', 'total_price', 'created_at')->get();
    }

    public function headings(): array {
        return ['ID', 'Type Support', 'Prix Total', 'Date'];
    }
}