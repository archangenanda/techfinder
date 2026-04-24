<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('Users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Utilisateur créé avec succès !');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        $user->update($request->only(['name', 'email']));

        // Si un nouveau mot de passe est saisi
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'Utilisateur mis à jour !');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Utilisateur supprimé.');
    }
}
