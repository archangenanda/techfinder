<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Competence;
use Illuminate\Http\Request;

class CompetenceController extends Controller
{
    /**
     * Affiche la liste des compétences avec pagination.
     */
    public function index()
    {
        // On utilise la pagination (10 par page) pour ton tableau
        $Competence_list = Competence::paginate(10);
        return view('Competence', compact('Competence_list'));
    }

    /**
     * Enregistre une nouvelle compétence.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label_comp' => 'required|string|max:255',
            'description_comp' => 'nullable|string'
        ]);

        Competence::create($request->all());

        // Redirection avec message pour le TOAST
        return redirect()->back()->with('success', 'Nouvelle compétence ajoutée avec succès !');
    }

    /**
     * Met à jour une compétence (via le Modal).
     */
    public function update(Request $request, int $code_comp)
    {
        $request->validate([
            'label_comp' => 'required|string|max:255',
            'description_comp' => 'nullable|string'
        ]);

        try {
            $competence = Competence::findOrFail($code_comp);
            $competence->update($request->all());

            // Redirection avec message pour le TOAST
            return redirect()->back()->with('success', 'La compétence a été modifiée !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la modification.');
        }
    }

    /**
     * Supprime une compétence.
     */
    public function destroy(int $code_comp)
    {
        try {
            $competence = Competence::findOrFail($code_comp);
            $competence->delete();

            // Redirection avec message pour le TOAST
            return redirect()->back()->with('success', 'Compétence supprimée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette compétence.');
        }
    }
}
