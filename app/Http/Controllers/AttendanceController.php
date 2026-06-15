<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingSession;
use App\Models\AttendanceSheet;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * D3. Planifier une nouvelle session de cours
     */
    public function storeSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_id'  => 'required|exists:trainings,id',
            'title'        => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time'   => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $session = TrainingSession::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Session de cours planifiée avec succès !',
            'data' => $session
        ], 201);
    }

    /**
     * D3. Enregistrer les présences/absences pour une session entière
     */
    public function submitAttendance(Request $request, $sessionId)
    {
        $session = TrainingSession::find($sessionId);
        if (!$session) {
            return response()->json(['status' => 'error', 'message' => 'Session introuvable.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'attendances' => 'required|array',
            'attendances.*.training_enrollment_id' => 'required|exists:training_enrollments,id',
            'attendances.*.status' => 'required|in:présent,absent,retard',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        foreach ($request->attendances as $attendanceData) {
            // Met à jour si ça existe déjà pour cette session, sinon le crée
            AttendanceSheet::updateOrCreate(
                [
                    'training_session_id' => $sessionId,
                    'training_enrollment_id' => $attendanceData['training_enrollment_id']
                ],
                [
                    'status' => $attendanceData['status']
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Feuille de présence enregistrée avec succès pour cette session !'
        ], 200);
    }

    /**
     * D3. Calculer le taux de présence d'un apprenant sur une formation
     */
    public function getStudentAttendanceRate($enrollmentId)
    {
        $enrollment = TrainingEnrollment::with(['customer', 'training'])->find($enrollmentId);
        if (!$enrollment) {
            return response()->json(['status' => 'error', 'message' => 'Inscription introuvable.'], 404);
        }

        // 1. Compter le nombre total de sessions passées pour cette formation
        $totalSessions = TrainingSession::where('training_id', $enrollment->training_id)->count();

        if ($totalSessions === 0) {
            return response()->json([
                'status' => 'success',
                'student' => $enrollment->customer->name,
                'training' => $enrollment->training->title,
                'attendance_rate' => 100, // Pas encore de cours, donc 100% par défaut
                'message' => 'Aucune session n\'a encore été planifiée pour cette formation.'
            ], 200);
        }

        // 2. Compter combien de fois l'apprenant a été marqué 'présent' ou 'retard'
        $presentCount = AttendanceSheet::where('training_enrollment_id', $enrollmentId)
            ->whereIn('status', ['présent', 'retard'])
            ->count();

        // 3. Calcul mathématique du taux d'assiduité
        $rate = ($presentCount / $totalSessions) * 100;

        return response()->json([
            'status' => 'success',
            'student' => $enrollment->customer->name,
            'training' => $enrollment->training->title,
            'total_sessions' => $totalSessions,
            'sessions_attended' => $presentCount,
            'attendance_rate' => round($rate, 2), // Arrondi à 2 décimales
            'eligible_for_certificate' => $rate >= 70 // Utile pour le module D4 !
        ], 200);
    }

    /**
     * D4. Générer et télécharger l'attestation de formation en PDF (Règle d'assiduité >= 70%)
     */
    public function downloadCertificate($enrollmentId)
    {
        $enrollment = TrainingEnrollment::with(['customer', 'training'])->find($enrollmentId);
        if (!$enrollment) {
            return response()->json(['status' => 'error', 'message' => 'Inscription introuvable.'], 404);
        }

        // 1. Calculer le taux réel d'assiduité
        $totalSessions = TrainingSession::where('training_id', $enrollment->training_id)->count();
        if ($totalSessions === 0) {
            return response()->json(['status' => 'error', 'message' => 'Impossible de générer l\'attestation : aucune session n\'a eu lieu.'], 400);
        }

        $presentCount = AttendanceSheet::where('training_enrollment_id', $enrollmentId)
            ->whereIn('status', ['présent', 'retard'])
            ->count();

        $attendanceRate = ($presentCount / $totalSessions) * 100;

        // 2. RÈGLE MÉTIER : Bloquer la génération sous les 70%
        if ($attendanceRate < 70) {
            return response()->json([
                'status' => 'error',
                'message' => "Génération refusée. L'apprenant possède un taux d'assiduité de " . round($attendanceRate, 2) . "%, ce qui est inférieur au seuil obligatoire de 70% pour obtenir l'attestation."
            ], 403);
        }

        // 3. Si éligible, générer le PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.certificate', [
            'enrollment' => $enrollment,
            'attendanceRate' => round($attendanceRate, 2)
        ])->setPaper('a4', 'landscape'); // Format paysage pour faire un vrai diplôme !

        return $pdf->download("attestation_formation_{$enrollmentId}.pdf");
    }
}