<?php

namespace Tests\Feature;

use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Competence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InterventionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test get all interventions list
     */
    public function test_interventions_list(): void
    {
        Intervention::factory(5)->create();

        $response = $this->get('/api/interventions');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test create new intervention
     */
    public function test_create_intervention(): void
    {
        $client = Utilisateur::factory()->create();
        $technician = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        $interventionData = [
            'date_int' => now()->toDateString(),
            'note_int' => 15,
            'commentaire_int' => 'Intervention test',
            'code_user_client' => $client->code_user,
            'code_user_techn' => $technician->code_user,
            'code_comp' => $competence->code_comp
        ];

        $response = $this->post('/api/interventions', $interventionData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('intervention', [
            'code_user_client' => $client->code_user,
            'code_comp' => $competence->code_comp
        ]);
    }

    /**
     * Test create intervention with missing required fields
     */
    public function test_create_intervention_missing_required(): void
    {
        $interventionData = [
            'commentaire_int' => 'Missing required fields'
        ];

        $response = $this->postJson('/api/interventions', $interventionData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_int', 'code_user_client', 'code_user_techn', 'code_comp']);
    }

    /**
     * Test create intervention with invalid date
     */
    public function test_create_intervention_invalid_date(): void
    {
        $client = Utilisateur::factory()->create();
        $technician = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        $interventionData = [
            'date_int' => 'invalid-date',
            'note_int' => 15,
            'commentaire_int' => 'Test',
            'code_user_client' => $client->code_user,
            'code_user_techn' => $technician->code_user,
            'code_comp' => $competence->code_comp
        ];

        $response = $this->postJson('/api/interventions', $interventionData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_int']);
    }

    /**
     * Test create intervention with invalid note (>20)
     */
    public function test_create_intervention_invalid_note(): void
    {
        $client = Utilisateur::factory()->create();
        $technician = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        $interventionData = [
            'date_int' => now()->toDateString(),
            'note_int' => 25, // Invalid: > 20
            'commentaire_int' => 'Test',
            'code_user_client' => $client->code_user,
            'code_user_techn' => $technician->code_user,
            'code_comp' => $competence->code_comp
        ];

        $response = $this->postJson('/api/interventions', $interventionData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['note_int']);
    }

    /**
     * Test get specific intervention by ID
     */
    public function test_get_intervention_by_id(): void
    {
        $intervention = Intervention::factory()->create();

        $response = $this->get("/api/interventions/{$intervention->code_int}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'code_int' => $intervention->code_int
        ]);
    }

    /**
     * Test get non-existent intervention
     */
    public function test_get_non_existent_intervention(): void
    {
        $response = $this->get('/api/interventions/99999');

        $response->assertStatus(404);
    }

    /**
     * Test update intervention
     */
    public function test_update_intervention(): void
    {
        $intervention = Intervention::factory()->create();
        $updateData = [
            'note_int' => 18,
            'commentaire_int' => 'Updated comment'
        ];

        $response = $this->put("/api/interventions/{$intervention->code_int}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('intervention', [
            'code_int' => $intervention->code_int,
            'note_int' => 18
        ]);
    }

    /**
     * Test delete intervention
     */
    public function test_delete_intervention(): void
    {
        $intervention = Intervention::factory()->create();

        $response = $this->delete("/api/interventions/{$intervention->code_int}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('intervention', ['code_int' => $intervention->code_int]);
    }

    /**
     * Test search interventions
     */
    public function test_search_interventions(): void
    {
        $client = Utilisateur::factory()->create(['code_user' => 'CLIENT001']);
        $technician = Utilisateur::factory()->create(['code_user' => 'TECH001']);
        $competence = Competence::factory()->create();

        Intervention::factory()->create([
            'code_user_client' => 'CLIENT001',
            'commentaire_int' => 'Test search comment'
        ]);

        $response = $this->get('/api/interventions/search?keyword=CLIENT001');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test search interventions missing keyword
     */
    public function test_search_interventions_missing_keyword(): void
    {
        $response = $this->getJson('/api/interventions/search');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['keyword']);
    }

    /**
     * Test intervention relationships
     */
    public function test_intervention_relationships(): void
    {
        $intervention = Intervention::factory()->create();

        $this->assertTrue(method_exists($intervention, 'client'));
        $this->assertTrue(method_exists($intervention, 'technicien'));
        $this->assertTrue(method_exists($intervention, 'competence'));
    }

    /**
     * Test intervention with valid note range
     */
    public function test_intervention_note_range(): void
    {
        $client = Utilisateur::factory()->create();
        $technician = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        // Test note 0
        $response = $this->post('/api/interventions', [
            'date_int' => now()->toDateString(),
            'note_int' => 0,
            'code_user_client' => $client->code_user,
            'code_user_techn' => $technician->code_user,
            'code_comp' => $competence->code_comp
        ]);
        $response->assertStatus(201);

        // Test note 20
        $response = $this->post('/api/interventions', [
            'date_int' => now()->toDateString(),
            'note_int' => 20,
            'code_user_client' => $client->code_user,
            'code_user_techn' => $technician->code_user,
            'code_comp' => $competence->code_comp
        ]);
        $response->assertStatus(201);
    }

    /**
     * Test intervention with null note (should use default of 0, not null)
     */
    public function test_intervention_null_note(): void
    {
        $client = Utilisateur::factory()->create();
        $technician = Utilisateur::factory()->create();
        $competence = Competence::factory()->create();

        $response = $this->post('/api/interventions', [
            'date_int' => now()->toDateString(),
            'code_user_client' => $client->code_user,
            'code_user_techn' => $technician->code_user,
            'code_comp' => $competence->code_comp
        ]);

        $response->assertStatus(201);
    }
}
