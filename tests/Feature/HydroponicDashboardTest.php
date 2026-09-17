<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class HydroponicDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_can_be_rendered_for_authorized_users()
    {
        $user = User::factory()->create([
            'role_agri' => 'it_admin',
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($user)->get('/hydroponics/dashboard');
        $response->assertStatus(200);
    }

    public function test_dashboard_redirects_unauthenticated_users()
    {
        $response = $this->get('/hydroponics/dashboard');
        $response->assertRedirect('/login');
    }
}

