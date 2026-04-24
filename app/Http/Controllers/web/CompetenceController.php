<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competence;

class CompetenceController extends Controller
{
    public function index()
    {
        $Competence_list = Competence::paginate(10);
        // Vérifie bien que ton fichier s'appelle Competence.blade.php
        return view('Competence', compact('Competence_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label_comp' => 'required',
            'description_comp' => 'required'
        ]);

        Competence::create($request->all());

        // On redirige vers la liste (index)
return redirect()->back()->with('success', 'Bravo ! C\'est enregistré.');    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label_comp' => 'required',
            'description_comp' => 'required'
        ]);

        $comp = Competence::findOrFail($id);
        $comp->update($request->all());
return redirect()->back()->with('success', 'La compétence a été mise à jour !');;
    }

    public function destroy($id)
    {
        $comp = Competence::findOrFail($id);
        $comp->delete();
        return redirect()->back()->with('success', 'Compétence supprimée définitivement.');

    }
}
