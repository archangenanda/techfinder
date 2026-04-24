<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserCompetence;
use App\Models\Utilisateur;
use App\Models\Competence;

class UserCompetenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = Utilisateur::all();
        $competences = Competence::all();

        // Create unique user-competence associations
        // Each user can have multiple competences
        foreach ($users as $user) {
            // Assign 2-5 random competences to each user
            $competencesToAssign = $competences->random(min(5, $competences->count()));

            foreach ($competencesToAssign as $competence) {
                // Only create if not already exists
                UserCompetence::firstOrCreate(
                    [
                        'code_user' => $user->code_user,
                        'code_comp' => $competence->code_comp,
                    ]
                );
            }
        }
    }
}

