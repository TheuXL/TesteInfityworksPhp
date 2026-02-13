<?php

namespace Tests\Feature;

use Tests\TestCase;

/** Teste da página inicial (welcome). */
class ExampleTest extends TestCase
{
    public function test_welcome_page_returns_success(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
