<?php

namespace App\Http\Controllers;

use App\Models\UserCompetence;
use Illuminate\Http\Request;

class UserCompetenceController extends Controller
{
    // Lister toutes les associations
    public function index()
    {
        try {
            $all = UserCompetence::all();
            return response()->json($all, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve associations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Ajouter une nouvelle association
    public function store(Request $request)
    {
        $request->validate([
            'code_user' => 'required|string|exists:utilisateur,code_user',
            'code_comp' => 'required|integer|exists:competences,code_comp'
        ]);

        // Vérifier si l'association existe déjà
        $exists = UserCompetence::where('code_user', $request->code_user)
            ->where('code_comp', $request->code_comp)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Association already exists'], 409);
        }

        try {
            UserCompetence::insert([
                'code_user' => $request->code_user,
                'code_comp' => $request->code_comp,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'User-Competence added successfully',
                'code_user' => $request->code_user,
                'code_comp' => $request->code_comp
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create association',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Afficher une seule association
    public function show(Request $request)
    {
        $request->validate([
            'code_user' => 'required|string',
            'code_comp' => 'required|integer',
        ]);

        try {
            $userComp = UserCompetence::where('code_user', $request->code_user)
                                      ->where('code_comp', $request->code_comp)
                                      ->first();

            if (!$userComp) {
                return response()->json(['message' => 'Association not found'], 404);
            }

            return response()->json($userComp, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve association',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Mettre à jour une association
    public function update(Request $request)
    {
        $request->validate([
            'old_code_user' => 'required|string',
            'old_code_comp' => 'required|integer',
            'code_user' => 'required|string|exists:utilisateur,code_user',
            'code_comp' => 'required|integer|exists:competences,code_comp',
        ]);

        try {
            // Check if old association exists
            if (!UserCompetence::where('code_user', $request->old_code_user)
                              ->where('code_comp', $request->old_code_comp)
                              ->exists()) {
                return response()->json(['message' => 'Association not found'], 404);
            }

            // Delete old and insert new in a database transaction
            \DB::transaction(function () use ($request) {
                // Delete old association
                UserCompetence::where('code_user', $request->old_code_user)
                              ->where('code_comp', $request->old_code_comp)
                              ->delete();

                // Insert new association
                \DB::table('user_competence')->insert([
                    'code_user' => $request->code_user,
                    'code_comp' => $request->code_comp,
                    'created_at' => \DB::raw('CURRENT_TIMESTAMP'),
                    'updated_at' => \DB::raw('CURRENT_TIMESTAMP'),
                ]);
            });

            return response()->json([
                'message' => 'Association updated successfully',
                'code_user' => $request->code_user,
                'code_comp' => $request->code_comp
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update association',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Supprimer une association
    public function destroy(Request $request)
    {
        $request->validate([
            'code_user' => 'required|string',
            'code_comp' => 'required|integer',
        ]);

        try {
            $deleted = UserCompetence::where('code_user', $request->code_user)
                                      ->where('code_comp', $request->code_comp)
                                      ->delete();

            if ($deleted === 0) {
                return response()->json(['message' => 'Association not found'], 404);
            }

            return response()->json(['message' => 'Association deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete association',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Find all competences for a specific user
    public function findByUser(Request $request)
    {
        $request->validate([
            'code_user' => 'required|string'
        ]);

        try {
            $competences = UserCompetence::where('code_user', $request->code_user)
                                         ->with('competence')
                                         ->get();

            if ($competences->isEmpty()) {
                return response()->json([
                    'message' => 'No competences found for this user',
                    'code_user' => $request->code_user
                ], 404);
            }

            return response()->json($competences, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve competences',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Find all users for a specific competence
    public function findByCompetence(Request $request)
    {
        $request->validate([
            'code_comp' => 'required|integer'
        ]);

        try {
            $users = UserCompetence::where('code_comp', $request->code_comp)
                                   ->with('user')
                                   ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No users found for this competence',
                    'code_comp' => $request->code_comp
                ], 404);
            }

            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve users',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}


