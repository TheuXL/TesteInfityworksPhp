<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes da página de relatórios (admin): acesso permitido só para admin. */
class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_reports_page(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
    }

    public function test_student_cannot_access_reports(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('admin.reports.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->get(route('admin.reports.index'));
        $response->assertRedirect(route('login'));
    }
}
