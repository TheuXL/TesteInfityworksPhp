<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do filtro de alunos por nome e e-mail na listagem admin. */
class StudentFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_students_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        Student::factory()->create(['name' => 'João Silva', 'email' => 'joao@test.com']);
        Student::factory()->create(['name' => 'Maria Santos', 'email' => 'maria@test.com']);

        $response = $this->actingAs($admin)->get(route('admin.students.index', ['search' => 'João']));
        $response->assertStatus(200);
        $response->assertSee('João Silva');
        $response->assertDontSee('Maria Santos');
    }

    public function test_admin_can_filter_students_by_email(): void
    {
        $admin = User::factory()->admin()->create();
        Student::factory()->create(['name' => 'João', 'email' => 'joao.unique@test.com']);
        Student::factory()->create(['name' => 'Maria', 'email' => 'maria.unique@test.com']);

        $response = $this->actingAs($admin)->get(route('admin.students.index', ['search' => 'joao.unique']));
        $response->assertStatus(200);
        $response->assertSee('joao.unique@test.com');
    }
}
