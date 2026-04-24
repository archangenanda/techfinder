<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Utilisateur;
use App\Models\Competence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intervention>
 */
class InterventionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_int' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'note_int' => $this->faker->numberBetween(0, 20),
            'commentaire_int' => $this->faker->paragraph(),
            'code_user_client' => Utilisateur::inRandomOrder()->first()?->code_user ?? Utilisateur::factory()->create()->code_user,
            'code_user_techn' => Utilisateur::inRandomOrder()->first()?->code_user ?? Utilisateur::factory()->create()->code_user,
            'code_comp' => Competence::inRandomOrder()->first()?->code_comp ?? Competence::factory()->create()->code_comp,
        ];
    }
}
