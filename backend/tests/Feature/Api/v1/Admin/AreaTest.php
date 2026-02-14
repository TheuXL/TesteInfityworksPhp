<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Áreas via API v1 (admin). */
class AreaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_see_areas_index(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/areas');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_create_area(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/areas', [
            'name' => 'Biologia',
        ]);
        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Biologia']);
        $this->assertDatabaseHas('areas', ['name' => 'Biologia']);
    }

    public function test_admin_can_update_area(): void
    {
        $area = Area::create(['name' => 'Física']);
        $response = $this->actingAs($this->admin)->putJson("/api/v1/admin/areas/{$area->id}", [
            'name' => 'Física Aplicada',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('areas', ['id' => $area->id, 'name' => 'Física Aplicada']);
    }

    public function test_admin_can_delete_area(): void
    {
        $area = Area::create(['name' => 'Química']);
        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/admin/areas/{$area->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }

    public function test_student_cannot_access_admin_areas_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/areas');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_areas_index(): void
    {
        $response = $this->getJson('/api/v1/admin/areas');
        $response->assertStatus(401);
    }
}
