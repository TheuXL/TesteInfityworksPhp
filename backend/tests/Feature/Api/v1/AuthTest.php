<?php

namespace Tests\Feature\Api\v1;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes de autenticação: login, registro, logout. UI no frontend; GET login/register redirecionam. */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function frontendUrl(): string
    {
        return config('app.frontend_url', 'http://localhost:5173');
    }

    public function test_guest_get_login_redirects_to_frontend(): void
    {
        $response = $this->get(route('login'));
        $response->assertRedirect($this->frontendUrl() . '/login');
    }

    public function test_guest_get_register_redirects_to_frontend(): void
    {
        $response = $this->get(route('register'));
        $response->assertRedirect($this->frontendUrl() . '/register');
    }

    public function test_admin_can_login_and_redirects_to_frontend_admin(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@test.com']);
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect($this->frontendUrl() . '/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_student_can_login_and_redirects_to_frontend_aluno(): void
    {
        $user = User::factory()->student()->create(['email' => 'aluno@test.com']);
        $response = $this->post(route('login'), [
            'email' => 'aluno@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect($this->frontendUrl() . '/aluno/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->admin()->create(['email' => 'admin@test.com']);
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->post(route('login'), []);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_user_can_register_as_student(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Novo Aluno',
            'email' => 'novo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect($this->frontendUrl() . '/aluno/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'novo@test.com']);
        $user = User::where('email', 'novo@test.com')->first();
        $this->assertEquals(UserRole::STUDENT, $user->role);
        $this->assertAuthenticatedAs($user);
    }

    public function test_register_requires_name_email_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'invalid',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->student()->create(['email' => 'existente@test.com']);
        $response = $this->post(route('register'), [
            'name' => 'Outro',
            'email' => 'existente@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->admin()->create();
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect($this->frontendUrl() . '/login');
        $this->assertGuest();
    }

    public function test_guest_cannot_access_admin_dashboard_api(): void
    {
        $response = $this->getJson('/api/v1/admin/dashboard');
        $response->assertStatus(401);
    }

    public function test_guest_cannot_access_aluno_dashboard_api(): void
    {
        $response = $this->getJson('/api/v1/aluno/dashboard');
        $response->assertStatus(401);
    }
}
