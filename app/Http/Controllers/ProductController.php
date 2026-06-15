<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // LÉGÈRE / RECHERCHE / FILTRES (Lire tout)
    public function index(Request $request)
    {
        $query = Product::with('supplier');

        // Filtre par catégorie
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Recherche par nom ou référence (sku)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->get()->map(function($product) {
            // Alerte visuelle : ajoute un indicateur si le stock < seuil d'alerte
            $product->is_low_stock = $product->stock_quantity < $product->alert_threshold;
            return $product;
        });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    // CRÉER un produit (avec upload de photo)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category' => 'required|in:matériel,consommable',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Max 2Mo
        ]);

        if ($request->hasFile('image')) {
            // Sauvegarde dans storage/app/public/products
            $path = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $path;
        }

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Produit créé avec succès !',
            'data' => $product
        ], 201);
    }

    // LIRE un seul produit
    public function show($id)
    {
        $product = Product::with('supplier')->find($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Produit introuvable'], 404);
        }

        $product->is_low_stock = $product->stock_quantity < $product->alert_threshold;

        return response()->json(['status' => 'success', 'data' => $product], 200);
    }

    // MODIFIER un produit
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Produit introuvable'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'category' => 'sometimes|in:matériel,consommable',
            'purchase_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'alert_threshold' => 'sometimes|integer|min:0',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $path;
        }

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Produit mis à jour avec succès !',
            'data' => $product
        ], 200);
    }

    // SUPPRIMER un produit
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Produit introuvable'], 404);
        }

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produit supprimé avec succès !'
        ], 200);
    }
}