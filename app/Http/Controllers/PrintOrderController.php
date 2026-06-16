<?php

namespace App\Http\Controllers;

use App\Models\PrintOrder;
use App\Models\PrintRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrintOrderController extends Controller
{
    /**
     * Créer un devis ou une commande d'impression (Calcul automatique et Upload)
     */
    public function store(Request $request)
    {
        // 1. Validation stricte des données d'entrée
        $request->validate([
            'customer_id'  => 'nullable|exists:customers,id',
            'support_type' => 'required|string',
            'width'        => 'nullable|numeric|min:1',  // Requis si m2
            'height'       => 'nullable|numeric|min:1', // Requis si m2
            'quantity'     => 'required|integer|min:1',
            'file'         => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240', // Max 10Mo
            'status'       => 'nullable|in:quote,pending,processing,ready,delivered'
        ]);

        // 2. Récupérer la grille tarifaire de l'admin pour ce support
        $rate = PrintRate::where('support_type', $request->support_type)->first();

        if (!$rate) {
            return response()->json([
                'status' => 'error',
                'message' => "Le type de support '{$request->support_type}' n'est pas configuré dans la table des tarifs."
            ], 422);
        }

        // 3. Calcul automatique du prix selon l'unité (m2 ou unitaire)
        $unitPrice = $rate->price;
        $totalPrice = 0;

        if ($rate->unit_type === 'm2') {
            if (!$request->width || !$request->height) {
                return response()->json([
                    'status' => 'error',
                    'message' => "La largeur et la hauteur en cm sont obligatoires pour le support : {$request->support_type}."
                ], 422);
            }
            // Surface en m² = (Largeur cm * Hauteur cm) / 10000
            $surfaceInM2 = ($request->width * $request->height) / 10000;
            $totalPrice = $surfaceInM2 * $unitPrice * $request->quantity;
        } else {
            // Calcul à l'unité simple
            $totalPrice = $unitPrice * $request->quantity;
        }

        // 4. Gestion de l'upload du fichier (S'il y en a un)
        $filePath = null;
        if ($request->hasFile('file')) {
            // Stockage dans storage/app/public/print_files
            $filePath = $request->file('file')->store('print_files', 'public');
        }

        // 5. Création de l'enregistrement
        $printOrder = PrintOrder::create([
            'customer_id'  => $request->customer_id,
            'support_type' => $request->support_type,
            'width'        => $request->width,
            'height'       => $request->height,
            'quantity'     => $request->quantity,
            'file_path'    => $filePath,
            'unit_price'   => $unitPrice,
            'total_price'  => $totalPrice,
            'status'       => $request->status ?? 'quote', // 'quote' par défaut (Devis)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $printOrder->status === 'quote' ? 'Devis calculé et enregistré !' : 'Commande d\'impression enregistrée !',
            'data' => $printOrder->load('customer')
        ], 201);
    }


    /**
     * Lister les commandes d'impression (avec filtre optionnel par statut pour le Kanban)
     */
    public function index(Request $request)
    {
        // On prépare la requête avec la relation client
        $query = PrintOrder::with('customer');

        // Si un statut est passé en paramètre (ex: ?status=processing), on filtre dessus
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $orders->count(),
            'data' => $orders
        ], 200);
    }

    /**
     * Afficher les détails d'un devis/commande
     */
    public function show($id)
    {
        $printOrder = PrintOrder::with('customer')->find($id);

        if (!$printOrder) {
            return response()->json(['status' => 'error', 'message' => 'Enregistrement introuvable.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $printOrder], 200);
    }

    /**
     * Mettre à jour le statut (Suivi de production / Conversion Devis -> Commande)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:quote,pending,processing,ready,delivered'
        ]);

        $printOrder = PrintOrder::find($id);

        if (!$printOrder) {
            return response()->json(['status' => 'error', 'message' => 'Enregistrement introuvable.'], 404);
        }

        $printOrder->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => "Le statut a été mis à jour avec succès : -> {$request->status}.",
            'data' => $printOrder
        ], 200);
    }

    /**
     * Générer et télécharger le bon de commande ou devis PDF
     */
    public function downloadPdf($id)
    {
        $order = PrintOrder::with('customer')->find($id);

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Enregistrement introuvable.'], 404);
        }

        // Charger la vue Blade qu'on vient de créer
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.print_order', compact('order'));

        // Téléchargement direct avec un nom dynamique
        $filename = $order->status === 'quote' ? "devis_impression_{$order->id}.pdf" : "bon_commande_{$order->id}.pdf";
        return $pdf->download($filename);
    }
}