<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    public function test_login_redirects_to_products()
    {
        $user = User::factory()->create([
            'password' => bcrypt('12345')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '12345'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('products');
    }

    public function test_unauthenticated_user_cannot_access_products(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
}
