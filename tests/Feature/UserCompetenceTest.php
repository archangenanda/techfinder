<?php

namespace Tests\Feature;

use App\Models\UserCompetence;
use App\Models\Utilisateur;
use App\Models\Competence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCompetenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test get all user competences
     */
    public function test_user_competences_list(): void
    {
        UserCompetence::factory()->create();

        $response = $this->get('/api/user-competences');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test create new user competence association
     */
    public function test_create_user_competence(): void
    {
        $user = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        $data = [
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp
        ];

        $response = $this->post('/api/user-competences', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp
        ]);
    }

    /**
     * Test create duplicate user competence fails
     */
    public function test_create_duplicate_user_competence(): void
    {
        $user = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        UserCompetence::insert([
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = [
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp
        ];

        $response = $this->post('/api/user-competences', $data);

        $response->assertStatus(409);
        $response->assertJsonFragment(['message' => 'Association already exists']);
    }

    /**
     * Test create with non-existent user
     */
    public function test_create_with_invalid_user(): void
    {
        $competence = Competence::factory()->create();

        $data = [
            'code_user' => 'NONEXIST',
            'code_comp' => $competence->code_comp
        ];

        $response = $this->postJson('/api/user-competences', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_user']);
    }

    /**
     * Test create with non-existent competence
     */
    public function test_create_with_invalid_competence(): void
    {
        $user = Utilisateur::factory()->create();

        $data = [
            'code_user' => $user->code_user,
            'code_comp' => 99999
        ];

        $response = $this->postJson('/api/user-competences', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_comp']);
    }

    /**
     * Test show specific user competence
     */
    public function test_show_user_competence(): void
    {
        $user = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        UserCompetence::insert([
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/api/user-competences/show?code_user=' . $user->code_user . '&code_comp=' . $competence->code_comp);

        $response->assertStatus(200);
    }

    /**
     * Test show non-existent association
     */
    public function test_show_non_existent_association(): void
    {
        $response = $this->get('/api/user-competences/show?code_user=NONEXIST&code_comp=99999');

        $response->assertStatus(404);
        $response->assertJsonFragment(['message' => 'Association not found']);
    }

    /**
     * Test update user competence
     */
    public function test_update_user_competence(): void
    {
        $user1 = Utilisateur::factory()->create();
        $user2 = Utilisateur::factory()->create();
        $comp1 = Competence::factory()->create();
        $comp2 = Competence::factory()->create();

        UserCompetence::insert([
            ['code_user' => $user1->code_user, 'code_comp' => $comp1->code_comp, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $data = [
            'old_code_user' => $user1->code_user,
            'old_code_comp' => $comp1->code_comp,
            'code_user' => $user2->code_user,
            'code_comp' => $comp2->code_comp
        ];

        $response = $this->putJson('/api/user-competences/update', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_competence', [
            'code_user' => $user2->code_user,
            'code_comp' => $comp2->code_comp
        ]);
    }

    /**
     * Test update non-existent association
     */
    public function test_update_non_existent_association(): void
    {
        $user = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        $data = [
            'old_code_user' => 'NONEXIST',
            'old_code_comp' => 99999,
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp
        ];

        $response = $this->put('/api/user-competences/update', $data);

        $response->assertStatus(404);
        $response->assertJsonFragment(['message' => 'Association not found']);
    }

    /**
     * Test delete user competence
     */
    public function test_delete_user_competence(): void
    {
        $user = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        UserCompetence::insert([
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->delete('/api/user-competences/delete', [
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_competence', [
            'code_user' => $user->code_user,
            'code_comp' => $competence->code_comp
        ]);
    }

    /**
     * Test delete non-existent association
     */
    public function test_delete_non_existent_association(): void
    {
        $response = $this->delete('/api/user-competences/delete', [
            'code_user' => 'NONEXIST',
            'code_comp' => 99999
        ]);

        $response->assertStatus(404);
        $response->assertJsonFragment(['message' => 'Association not found']);
    }

    /**
     * Test relationships
     */
    public function test_user_competence_relationships(): void
    {
        $uc = UserCompetence::factory()->create();

        $this->assertTrue(method_exists($uc, 'user'));
        $this->assertTrue(method_exists($uc, 'competence'));
    }

    /**
     * Test missing required fields
     */
    public function test_create_missing_required_fields(): void
    {
        $response = $this->postJson('/api/user-competences', [
            'code_user' => 'USER001'
            // Missing code_comp
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_comp']);
    }
}
