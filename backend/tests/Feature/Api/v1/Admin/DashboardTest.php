<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do dashboard admin via API v1: acesso permitido só para admin. */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');
        $response->assertStatus(200);
        $response->assertJsonStructure(['chart_data']);
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->getJson('/api/v1/admin/dashboard');
        $response->assertStatus(401);
    }
}
