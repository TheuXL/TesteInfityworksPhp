<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Áreas (admin): listar, criar, editar, excluir e controle de acesso. */
class AdminAreaTest extends TestCase
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
        $response = $this->actingAs($this->admin)->get(route('admin.areas.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.areas.index');
    }

    public function test_admin_can_create_area(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.areas.store'), [
            'name' => 'Biologia',
        ]);
        $response->assertRedirect(route('admin.areas.index'));
        $this->assertDatabaseHas('areas', ['name' => 'Biologia']);
    }

    public function test_admin_can_update_area(): void
    {
        $area = Area::create(['name' => 'Física']);
        $response = $this->actingAs($this->admin)->put(route('admin.areas.update', $area), [
            'name' => 'Física Aplicada',
        ]);
        $response->assertRedirect(route('admin.areas.index'));
        $this->assertDatabaseHas('areas', ['id' => $area->id, 'name' => 'Física Aplicada']);
    }

    public function test_admin_can_delete_area(): void
    {
        $area = Area::create(['name' => 'Química']);
        $response = $this->actingAs($this->admin)->delete(route('admin.areas.destroy', $area));
        $response->assertRedirect(route('admin.areas.index'));
        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }

    public function test_student_cannot_access_admin_areas_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('admin.areas.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_areas_index(): void
    {
        $response = $this->get(route('admin.areas.index'));
        $response->assertRedirect(route('login'));
    }
}
