<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * 1. Lister tous les clients
     */
    public function index()
    {
        $customers = Customer::orderBy('name', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $customers
        ], 200);
    }

    /**
     * 2. Enregistrer un nouveau client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|unique:customers,email',
            'address' => 'nullable|string',
            'balance' => 'numeric', // Solde initial (crédit) si nécessaire
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Client créé avec succès !',
            'data' => $customer
        ], 201);
    }

    /**
     * 3. Afficher les détails d'un client spécifique
     */
    public function show($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client introuvable.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ], 200);
    }

    /**
     * 4. Mettre à jour les informations d'un client
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client introuvable.'
            ], 404);
        }

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => "nullable|email|unique:customers,email,{$id}",
            'address' => 'nullable|string',
            'balance' => 'numeric',
        ]);

        $customer->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Informations du client mises à jour !',
            'data' => $customer
        ], 200);
    }

    /**
     * 5. Supprimer un client
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client introuvable.'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Client supprimé avec succès.'
        ], 200);
    }

    /**
     * 6. Historique de toutes les commandes d'un client (Objectif B3-Point 2)
     */
    public function orderHistory($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client introuvable.'
            ], 404);
        }

        // Récupère toutes les commandes du client avec les lignes d'articles associées
        $orders = Order::with('items.product')
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'customer' => $customer->name,
            'orders_count' => $orders->count(),
            'data' => $orders
        ], 200);
    }
}