<?php

namespace App\Exports;

use App\Models\TrainingEnrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TrainingsExport implements FromCollection, WithHeadings
{
    public function collection() {
        // Retourne les inscriptions avec le nom de la formation liée
        return TrainingEnrollment::with('training:id,title')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'formation' => $enrollment->training->title ?? 'N/A',
                    'montant' => $enrollment->amount_paid,
                    'date' => $enrollment->created_at->format('d/m/Y'),
                ];
            });
    }

    public function headings(): array {
        return ['ID', 'Formation', 'Montant Payé', 'Date Inscription'];
    }
}