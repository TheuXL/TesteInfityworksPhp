<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes da API de relatórios (admin) v1: acesso permitido só para admin. */
class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_reports_page(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->getJson('/api/v1/admin/reports');
        $response->assertStatus(200);
        $response->assertJsonStructure(['report', 'chart_data']);
    }

    public function test_student_cannot_access_reports(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/reports');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->getJson('/api/v1/admin/reports');
        $response->assertStatus(401);
    }
}
