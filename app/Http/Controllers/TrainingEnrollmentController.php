<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingEnrollmentController extends Controller
{
    /**
     * D2. Lister toutes les inscriptions avec détails (Formations et Clients)
     */
    public function index()
    {
        $enrollments = TrainingEnrollment::with(['customer', 'training'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $enrollments->count(),
            'data' => $enrollments
        ], 200);
    }

    /**
     * D2. Inscrire un apprenant à une formation (avec vérification des places disponibles)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'    => 'required|exists:customers,id',
            'training_id'    => 'required|exists:trainings,id',
            'payment_status' => 'required|in:payé,partiel,non payé',
            'amount_paid'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 1. Récupérer la formation ciblée
        $training = Training::findOrFail($request->training_id);

        // 2. Compter le nombre d'inscriptions existantes pour cette formation
        $currentEnrollmentsCount = $training->enrollments()->count();

        // 3. RÈGLE MÉTIER : Bloquer si la capacité maximale est atteinte
        if ($currentEnrollmentsCount >= $training->max_capacity) {
            return response()->json([
                'status' => 'error',
                'message' => "Inscription impossible. La capacité maximale de cette formation ({$training->max_capacity} places) a été atteinte."
            ], 422);
        }

        // 4. Vérifier si l'apprenant n'est pas déjà inscrit pour éviter les doublons
        $alreadyEnrolled = TrainingEnrollment::where('customer_id', $request->customer_id)
            ->where('training_id', $request->training_id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cet apprenant est déjà inscrit à cette formation.'
            ], 422);
        }

        // 5. Créer l'inscription
        $enrollment = TrainingEnrollment::create([
            'customer_id'    => $request->customer_id,
            'training_id'    => $request->training_id,
            'payment_status' => $request->payment_status,
            'amount_paid'    => $request->amount_paid,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Apprenant inscrit avec succès à la formation !',
            'data' => $enrollment->load(['customer', 'training'])
        ], 201);
    }

    /**
     * D2. Mettre à jour le statut de paiement ou d'inscription
     */
    public function update(Request $request, $id)
    {
        $enrollment = TrainingEnrollment::find($id);

        if (!$enrollment) {
            return response()->json(['status' => 'error', 'message' => 'Inscription introuvable.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_status' => 'sometimes|required|in:payé,partiel,non payé',
            'amount_paid'    => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $enrollment->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Données d\'inscription mises à jour !',
            'data' => $enrollment->load(['customer', 'training'])
        ], 200);
    }

    /**
     * D2. Générer et télécharger le reçu d'inscription au format PDF
     */
    public function downloadReceipt($id)
    {
        $enrollment = TrainingEnrollment::with(['customer', 'training'])->find($id);

        if (!$enrollment) {
            return response()->json(['status' => 'error', 'message' => 'Inscription introuvable.'], 404);
        }

        // Charger la vue Blade dédiée au reçu
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.training_receipt', compact('enrollment'));

        // Retourner le téléchargement direct du PDF
        return $pdf->download("recu_inscription_{$enrollment->id}.pdf");
    }
}