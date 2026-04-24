<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intervention;

class InterventionController extends Controller
{

//lister toutes les interventions
    public function index()
    {
        $interventions = Intervention::all();
        return response()->json($interventions, 200);
    }

    //créer une nouvelle intervention
    public function store(Request $request)
    {
        $request->validate([
            'date_int' => 'required|date',
            'note_int' => 'nullable|integer|min:0|max:20',
            'commentaire_int' => 'nullable|string',
            'code_user_client' => 'required|string',
            'code_user_techn' => 'required|string',
            'code_comp' => 'required|integer'
        ]);

        $intervention = Intervention::create($request->all());

        return response()->json([
            'message' => 'Intervention créée avec succès',
            'data' => $intervention
        ], 201);
    }

    //afficher une intervention spécifique
    public function show(Intervention $intervention)
    {
        return response()->json([
            'message' => 'Intervention récupérée avec succès',
            'data' => $intervention
        ], 200);
    }

    //mettre à jour une intervention
    public function update(Request $request, Intervention $intervention)
    {
        $request->validate([
            'date_int' => 'sometimes|date',
            'note_int' => 'nullable|integer|min:0|max:20',
            'commentaire_int' => 'nullable|string',
            'code_user_client' => 'sometimes|string',
            'code_user_techn' => 'sometimes|string',
            'code_comp' => 'sometimes|integer'
        ]);

        $intervention->update($request->only([
            'date_int',
            'note_int',
            'commentaire_int',
            'code_user_client',
            'code_user_techn',
            'code_comp'
        ]));

        return response()->json([
            'message' => 'Intervention mise à jour',
            'data' => $intervention
        ], 200);
    }

    //supprimer une intervention
    public function destroy(Intervention $intervention)
    {
        $intervention->delete();
        return response()->json([
            'message' => 'Intervention supprimée avec succès',
            'data' => $intervention
        ], 200);
    }

    //Rechercher une intervention
    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string'
        ]);

        $keyword = $request->keyword;

        $interventions = Intervention::where('commentaire_int', 'LIKE', "%$keyword%")
            ->orWhere('code_user_client', 'LIKE', "%$keyword%")
            ->orWhere('code_user_techn', 'LIKE', "%$keyword%")
            ->get();

        return response()->json($interventions, 200);
    }
}
