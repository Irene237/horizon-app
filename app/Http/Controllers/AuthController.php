<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Gestion de la connexion de l'utilisateur (API Login)
     */
    public function login(Request $request)
    {
        // 1. Validation des données entrantes
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();

        // 3. Vérifier le mot de passe
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Identifiants incorrects.'
            ], 401);
        }

        // 4. Créer un token d'accès avec Sanctum (idéal pour le mobile/web)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Retourner la réponse avec les infos et le rôle
        return response()->json([
            'status' => 'success',
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // Très important pour ton app Flutter plus tard
            ]
        ], 200);
    }
    public function register(Request $request)
{
    // 1. Validation des données de l'inscription
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
        'role' => 'required|string' // admin, vendeur, formateur, etc.
    ]);

    // 2. Création de l'utilisateur dans la base de données
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password), // On crypte le mot de passe !
        'role' => $request->role,
    ]);

    // 3. Génération du token pour connecter automatiquement l'utilisateur après inscription
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'message' => 'Utilisateur enregistré avec succès !',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ], 201); // 201 signifie "Créé avec succès"
}

    /**
     * Gestion de la déconnexion (Révocation du token)
     */
    public function logout(Request $request)
    {
        // Supprime le token actuel de l'utilisateur connecté
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Déconnexion réussie avec succès.'
        ], 200);
    }
}