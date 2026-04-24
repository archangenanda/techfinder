<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "code_user" => $this->faker->unique()->bothify("USER####"), // Génère un code utilisateur unique
            "nom_user" => $this->faker->lastName(),
            "prenom_user" => $this->faker->firstName(),
            "login_user" => $this->faker->unique()->userName(),
            "password_user" => bcrypt('password'), // Mot de passe par défaut
            "tel_user" => $this->faker->phoneNumber(),
            "sexe_user" => $this->faker->randomElement(['M', 'F']),
            "role_user" => $this->faker->randomElement(['client', 'technicien']),
            "etat_user" => $this->faker->randomElement(['actif', 'inactif', 'bloquer'])
        ];
    }
}
