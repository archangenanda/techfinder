<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return response()->json($utilisateurs, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code_user' => 'required|string|unique:utilisateur,code_user',
            'nom_user' => 'required|string|max:255',
            'prenom_user' => 'required|string|max:255',
            'login_user' => 'required|string|max:255|unique:utilisateur,login_user',
            'password_user' => 'required|string|min:6',
            'tel_user' => 'nullable|string|max:20',
            'sexe_user' => ['required', Rule::in(['M','F'])],
            'role_user' => 'required|string|max:50',
            'etat_user' => ['required', Rule::in(['actif','inactif'])],
        ]);

        $utilisateur = Utilisateur::create([
            'code_user' => $request->code_user,
            'nom_user' => $request->nom_user,
            'prenom_user' => $request->prenom_user,
            'login_user' => $request->login_user,
            'password_user' => bcrypt($request->password_user),
            'tel_user' => $request->tel_user,
            'sexe_user' => $request->sexe_user,
            'role_user' => $request->role_user,
            'etat_user' => $request->etat_user,
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'data' => $utilisateur
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code_user)
    {
        $utilisateur = Utilisateur::find($code_user);

        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json($utilisateur, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code_user)
    {
        $utilisateur = Utilisateur::find($code_user);

        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $request->validate([
            'nom_user' => 'sometimes|required|string|max:255',
            'prenom_user' => 'sometimes|required|string|max:255',
            'login_user' => ['sometimes','required','string','max:255',
                             Rule::unique('utilisateur')->ignore($code_user,'code_user')],
            'password_user' => 'sometimes|required|string|min:6',
            'tel_user' => 'nullable|string|max:20',
            'sexe_user' => ['sometimes','required', Rule::in(['M','F'])],
            'role_user' => 'sometimes|required|string|max:50',
            'etat_user' => ['sometimes','required', Rule::in(['actif','inactif'])],
        ]);

        $utilisateur->update([
            'nom_user' => $request->nom_user ?? $utilisateur->nom_user,
            'prenom_user' => $request->prenom_user ?? $utilisateur->prenom_user,
            'login_user' => $request->login_user ?? $utilisateur->login_user,
            'password_user' => $request->password_user ? bcrypt($request->password_user) : $utilisateur->password_user,
            'tel_user' => $request->tel_user ?? $utilisateur->tel_user,
            'sexe_user' => $request->sexe_user ?? $utilisateur->sexe_user,
            'role_user' => $request->role_user ?? $utilisateur->role_user,
            'etat_user' => $request->etat_user ?? $utilisateur->etat_user,
        ]);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $utilisateur
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code_user)
    {
        $utilisateur = Utilisateur::find($code_user);

        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $utilisateur->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès'], 200);
    }

    /**
     * Optional: Search utilisateurs by name, login, or role
     */
    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string'
        ]);

        $keyword = $request->keyword;

        $utilisateurs = Utilisateur::where('nom_user','LIKE',"%$keyword%")
            ->orWhere('prenom_user','LIKE',"%$keyword%")
            ->orWhere('login_user','LIKE',"%$keyword%")
            ->orWhere('role_user','LIKE',"%$keyword%")
            ->get();

        return response()->json($utilisateurs, 200);
    }
}
