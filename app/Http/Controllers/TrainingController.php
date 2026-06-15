<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    /**
     * D1. Lister toutes les formations du catalogue
     */
    public function index()
    {
        $trainings = Training::latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $trainings->count(),
            'data' => $trainings
        ], 200);
    }

    /**
     * D1. Ajouter une nouvelle formation au catalogue
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'level' => 'required|in:débutant,intermédiaire,avancé',
            'trainer_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_capacity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $training = Training::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Formation ajoutée au catalogue avec succès !',
            'data' => $training
        ], 201);
    }

    /**
     * D1. Afficher les détails d'une formation spécifique
     */
    public function show($id)
    {
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formation introuvable.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $training
        ], 200);
    }

    /**
     * D1. Modifier une formation existante
     */
    public function update(Request $request, $id)
    {
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formation introuvable.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duration_hours' => 'sometimes|required|integer|min:1',
            'level' => 'sometimes|required|in:débutant,intermédiaire,avancé',
            'trainer_name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'max_capacity' => 'sometimes|required|integer|min:1',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $training->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Formation mise à jour avec succès !',
            'data' => $training
        ], 200);
    }

    /**
     * D1. Supprimer une formation du catalogue
     */
    public function destroy($id)
    {
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formation introuvable.'
            ], 404);
        }

        $training->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Formation supprimée du catalogue.'
        ], 200);
    }
}