<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/api/categories/1');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                "id" => 1,
                "name" => "Maymie Marks",
                "status" => "disabled",
            ]
        ]);
    }
}
