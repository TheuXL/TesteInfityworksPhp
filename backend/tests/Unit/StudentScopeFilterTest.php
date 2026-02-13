<?php

namespace Tests\Unit;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do scope filter do modelo Student (busca por nome e e-mail). */
class StudentScopeFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_filter_applies_search_on_name_and_email(): void
    {
        Student::factory()->create(['name' => 'Ana Paula', 'email' => 'ana@mail.com']);
        Student::factory()->create(['name' => 'Bruno Costa', 'email' => 'bruno@mail.com']);

        $results = Student::filter(['search' => 'Ana'])->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Ana Paula', $results->first()->name);

        $results = Student::filter(['search' => 'bruno@mail'])->get();
        $this->assertCount(1, $results);
        $this->assertEquals('bruno@mail.com', $results->first()->email);
    }

    public function test_scope_filter_with_empty_search_returns_all(): void
    {
        Student::factory()->count(3)->create();
        $this->assertCount(3, Student::filter(['search' => ''])->get());
        $this->assertCount(3, Student::filter([])->get());
    }
}
