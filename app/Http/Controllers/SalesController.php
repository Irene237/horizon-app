<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    /**
     * Enregistrer une nouvelle vente (Point de Vente) avec gestion des crédits
     */
    public function store(Request $request)
    {
        // 1. Validation des données du panier et de la vente
        $request->validate([
            'customer_id'    => 'nullable|exists:customers,id',
            'discount'       => 'numeric|min:0',
            'discount_type'  => 'required|in:fixed,percentage',
            'payment_mode'   => 'required|in:cash,mobile_money,virement',
            'payment_status' => 'required|in:paid,credit',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        // Sécurité : Impossible d'accorder un crédit à un client anonyme
        if ($request->payment_status === 'credit' && !$request->customer_id) {
            return response()->json([
                'status' => 'error',
                'message' => "Impossible d'accorder un crédit à un client anonyme. Veuillez sélectionner un client."
            ], 400);
        }

        // On utilise une transaction DB pour sécuriser l'opération
        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $itemsToProcess = [];

            // 2. Vérification de la disponibilité des stocks et calcul du sous-total
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock insuffisant pour le produit : {$product->name}. Restant : {$product->stock_quantity}"
                    ], 400);
                }

                $itemPrice = $product->selling_price;
                $subtotal += $itemPrice * $item['quantity'];

                // On garde en mémoire pour l'enregistrement plus tard
                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice
                ];
            }

            // 3. Calcul de la remise et du total final
            $discountAmount = 0;
            if ($request->discount > 0) {
                if ($request->discount_type === 'percentage') {
                    $discountAmount = ($subtotal * $request->discount) / 100;
                } else {
                    $discountAmount = $request->discount;
                }
            }
            $total = max(0, $subtotal - $discountAmount);

            // 4. Création de la commande principale
            $order = Order::create([
                'customer_id'    => $request->customer_id,
                'user_id'        => auth()->id(), // Le vendeur connecté via Sanctum
                'subtotal'       => $subtotal,
                'discount'       => $discountAmount,
                'discount_type'  => $request->discount_type,
                'total'          => $total,
                'payment_mode'   => $request->payment_mode,
                'payment_status' => $request->payment_status,
            ]);

            // 5. Enregistrement des lignes du panier et déduction des stocks
            foreach ($itemsToProcess as $processItem) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $processItem['product']->id,
                    'quantity'   => $processItem['quantity'],
                    'price'      => $processItem['price'],
                ]);

                // Mise à jour automatique du stock du produit
                $processItem['product']->decrement('stock_quantity', $processItem['quantity']);
            }

            // 6. Gestion automatique du crédit sur le solde du client
            if ($order->payment_status === 'credit') {
                $customer = Customer::find($order->customer_id);
                $customer->increment('balance', $order->total);
            }

            // 7. Réponse avec la commande complète
            return response()->json([
                'status' => 'success',
                'message' => $order->payment_status === 'credit'
                    ? 'Vente à crédit enregistrée avec succès ! Le solde du client a été mis à jour.'
                    : 'Vente enregistrée avec succès ! Stock mis à jour.',
                'order' => $order->load('items')
            ], 201);
        });
    }

    /**
     * Générer et télécharger la facture PDF automatique
     */
    public function downloadInvoice($id)
    {
        // Récupérer la commande avec les détails des produits et du client associé
        $order = Order::with(['items.product', 'customer'])->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Commande introuvable.'
            ], 404);
        }

        // Charger le template Blade créé dans resources/views/invoices/invoice.blade.php
        $pdf = Pdf::loadView('invoices.invoice', compact('order'));

        // Forcer le téléchargement direct du document avec un nom personnalisé
        return $pdf->download("facture_horizon_{$order->id}.pdf");
    }
}