<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_loads_catalog_for_guest(): void
    {
        $this->withoutVite();
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
