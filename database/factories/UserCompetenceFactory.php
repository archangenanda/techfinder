<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Utilisateur;
use App\Models\Competence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCompetence>
 */
class UserCompetenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get or create multiple users and competences to ensure uniqueness
        $users = Utilisateur::all();
        if ($users->count() < 3) {
            $users = Utilisateur::factory(10)->create();
        }

        $competences = Competence::all();
        if ($competences->count() < 3) {
            $competences = Competence::factory(10)->create();
        }

        // Randomly select a user and competence
        $user = $users->random();
        $competence = $competences->random();

        return [
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp,
        ];
    }

    /**
     * State to ensure a specific user-competence pair
     */
    public function forUser(Utilisateur $user): static
    {
        return $this->state(fn (array $attributes) => [
            'code_user' => $user->code_user,
        ]);
    }

    /**
     * State to ensure a specific competence
     */
    public function forCompetence(Competence $competence): static
    {
        return $this->state(fn (array $attributes) => [
            'code_comp' => $competence->code_comp,
        ]);
    }
}

