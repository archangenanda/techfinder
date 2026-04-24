<?php

namespace Tests\Feature;

use App\Models\Competence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompetenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test get all competences list
     */
    public function test_competences_list(): void
    {
        // Create 5 competences
        Competence::factory(5)->create();

        $response = $this->get('/api/competences');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test create a new competence
     */
    public function test_create_competence(): void
    {
        $competenceData = [
            'label_comp' => 'PHP Development',
            'description_comp' => 'PHP backend development skills'
        ];

        $response = $this->post('/api/competences', $competenceData);

        $response->assertStatus(201);
        $response->assertJsonFragment($competenceData);
        $this->assertDatabaseHas('competences', $competenceData);
    }

    /**
     * Test create competence with missing required field
     */
    public function test_create_competence_missing_label(): void
    {
        $competenceData = [
            'description_comp' => 'Missing label'
        ];

        $response = $this->postJson('/api/competences', $competenceData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label_comp']);
    }

    /**
     * Test get specific competence by ID
     */
    public function test_get_competence_by_id(): void
    {
        $competence = Competence::factory()->create();

        $response = $this->get("/api/competences/{$competence->code_comp}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'code_comp' => $competence->code_comp,
            'label_comp' => $competence->label_comp
        ]);
    }

    /**
     * Test get non-existent competence
     */
    public function test_get_non_existent_competence(): void
    {
        $response = $this->get('/api/competences/99999');

        $response->assertStatus(500);
        $response->assertJsonFragment(['error' => 'Failed to retrieve competence']);
    }

    /**
     * Test update competence
     */
    public function test_update_competence(): void
    {
        $competence = Competence::factory()->create();
        $updatedData = [
            'label_comp' => 'Updated Label',
            'description_comp' => 'Updated description'
        ];

        $response = $this->put("/api/competences/{$competence->code_comp}", $updatedData);

        $response->assertStatus(200);
        $response->assertJsonFragment($updatedData);
        $this->assertDatabaseHas('competences', [
            'code_comp' => $competence->code_comp,
            'label_comp' => 'Updated Label'
        ]);
    }

    /**
     * Test delete competence
     */
    public function test_delete_competence(): void
    {
        $competence = Competence::factory()->create();

        $response = $this->delete("/api/competences/{$competence->code_comp}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('competences', ['code_comp' => $competence->code_comp]);
    }

    /**
     * Test search competences
     */
    public function test_search_competences(): void
    {
        Competence::factory()->create(['label_comp' => 'Laravel']);
        Competence::factory()->create(['label_comp' => 'Symfony']);
        Competence::factory()->create(['description_comp' => 'Laravel framework expertise']);

        $response = $this->get('/api/competences/search?keyword=Laravel');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test search with missing keyword
     */
    public function test_search_competences_missing_keyword(): void
    {
        $response = $this->getJson('/api/competences/search');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['keyword']);
    }

    /**
     * Test competence relationships
     */
    public function test_competence_has_utilisateurs(): void
    {
        $competence = Competence::factory()->create();

        // Verify the relationship method exists
        $this->assertTrue(method_exists($competence, 'utilisateurs'));
    }

    /**
     * Test competence has interventions
     */
    public function test_competence_has_interventions(): void
    {
        $competence = Competence::factory()->create();

        // Verify the relationship method exists
        $this->assertTrue(method_exists($competence, 'interventions'));
    }
}
