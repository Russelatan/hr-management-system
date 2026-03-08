<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_redirects_authenticated_users(): void
    {
        $user = User::factory()->employee()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/employee/dashboard');
    }
}
