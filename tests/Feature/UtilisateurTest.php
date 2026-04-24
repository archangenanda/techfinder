<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UtilisateurTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test get all utilisateurs list
     */
    public function test_utilisateurs_list(): void
    {
        Utilisateur::factory(5)->create();

        $response = $this->get('/api/utilisateurs');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test create new utilisateur
     */
    public function test_create_utilisateur(): void
    {
        $utilisateurData = [
            'code_user' => 'USER001',
            'nom_user' => 'Dupont',
            'prenom_user' => 'Jean',
            'login_user' => 'jean.dupont',
            'password_user' => 'password123',
            'tel_user' => '0612345678',
            'sexe_user' => 'M',
            'role_user' => 'client',
            'etat_user' => 'actif'
        ];

        $response = $this->post('/api/utilisateurs', $utilisateurData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('utilisateur', [
            'code_user' => 'USER001',
            'nom_user' => 'Dupont'
        ]);
    }

    /**
     * Test create utilisateur with duplicate code
     */
    public function test_create_utilisateur_duplicate_code(): void
    {
        Utilisateur::factory()->create(['code_user' => 'USER001']);

        $utilisateurData = [
            'code_user' => 'USER001',
            'nom_user' => 'Test',
            'prenom_user' => 'User',
            'login_user' => 'test.user',
            'password_user' => 'password123',
            'tel_user' => '0612345678',
            'sexe_user' => 'M',
            'role_user' => 'client',
            'etat_user' => 'actif'
        ];

        $response = $this->postJson('/api/utilisateurs', $utilisateurData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_user']);
    }

    /**
     * Test create utilisateur with invalid gender
     */
    public function test_create_utilisateur_invalid_gender(): void
    {
        $utilisateurData = [
            'code_user' => 'USER002',
            'nom_user' => 'Test',
            'prenom_user' => 'User',
            'login_user' => 'test.user',
            'password_user' => 'password123',
            'tel_user' => '0612345678',
            'sexe_user' => 'X', // Invalid: must be M or F
            'role_user' => 'client',
            'etat_user' => 'actif'
        ];

        $response = $this->postJson('/api/utilisateurs', $utilisateurData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sexe_user']);
    }

    /**
     * Test get specific utilisateur by code
     */
    public function test_get_utilisateur_by_code(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->get("/api/utilisateurs/{$utilisateur->code_user}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'code_user' => $utilisateur->code_user,
            'nom_user' => $utilisateur->nom_user
        ]);
    }

    /**
     * Test get non-existent utilisateur
     */
    public function test_get_non_existent_utilisateur(): void
    {
        $response = $this->get('/api/utilisateurs/NONEXIST');

        $response->assertStatus(404);
        $response->assertJsonFragment(['message' => 'Utilisateur non trouvé']);
    }

    /**
     * Test update utilisateur
     */
    public function test_update_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();
        $updateData = [
            'nom_user' => 'Updated Name',
            'prenom_user' => 'Updated Prenom',
            'tel_user' => '0698765432'
        ];

        $response = $this->put("/api/utilisateurs/{$utilisateur->code_user}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('utilisateur', [
            'code_user' => $utilisateur->code_user,
            'nom_user' => 'Updated Name'
        ]);
    }

    /**
     * Test delete utilisateur
     */
    public function test_delete_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->delete("/api/utilisateurs/{$utilisateur->code_user}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('utilisateur', ['code_user' => $utilisateur->code_user]);
    }

    /**
     * Test search utilisateurs
     */
    public function test_search_utilisateurs(): void
    {
        Utilisateur::factory()->create(['nom_user' => 'Jean', 'login_user' => 'jean.dupont']);
        Utilisateur::factory()->create(['nom_user' => 'Marie', 'login_user' => 'marie.martin']);

        $response = $this->get('/api/utilisateurs/search?keyword=Jean');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test utilisateur relationships
     */
    public function test_utilisateur_has_competences(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $this->assertTrue(method_exists($utilisateur, 'competences'));
    }

    /**
     * Test utilisateur has client interventions
     */
    public function test_utilisateur_has_client_interventions(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $this->assertTrue(method_exists($utilisateur, 'interventionsClient'));
    }

    /**
     * Test utilisateur has technician interventions
     */
    public function test_utilisateur_has_technician_interventions(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $this->assertTrue(method_exists($utilisateur, 'interventionsTechnicien'));
    }

    /**
     * Test password is hashed on creation
     */
    public function test_utilisateur_password_hashed(): void
    {
        $utilisateurData = [
            'code_user' => 'USER003',
            'nom_user' => 'Test',
            'prenom_user' => 'Password',
            'login_user' => 'test.password',
            'password_user' => 'myplainpassword',
            'tel_user' => '0612345678',
            'sexe_user' => 'F',
            'role_user' => 'technicien',
            'etat_user' => 'actif'
        ];

        $this->post('/api/utilisateurs', $utilisateurData);

        $utilisateur = Utilisateur::where('code_user', 'USER003')->first();
        $this->assertNotEquals('myplainpassword', $utilisateur->password_user);
    }
}
