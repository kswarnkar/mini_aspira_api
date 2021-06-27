<?php

namespace Tests\Feature;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Register User Test.
     * ./vendor/bin/phpunit 
     *
     * @return void
     */
    public function test_register()
    {
        $faker = Factory::create();
        // User's data
        $user = [
            'email' => $faker->email,
            'name' => $faker->name,
            'password' => 'password',
        ];
        // Send post request
        $response = $this->json('POST', route('api.register'), $user);
        // Assert it was successful
        $response->assertStatus(200)->json(['successfully register test user!']);
    }

    /**
     * Login User Test.
     *
     * @return void
     */
    public function test_login()
    {
        //Create user
        $user = User::factory()->create();
        //attempt login
        $response = $this->json('POST', route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        //Assert it was successful and a token was received
        $response->assertStatus(200)
            ->json([
                'message' => 'test user successfully login!',
                'data' => $response,
            ]);
        $this->assertArrayHasKey('token', $response->json());
    }

    /**
     * Logout User Test.
     *
     * @return void
     */
    public function test_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('api.logout'));

        $response->assertStatus(200);
    }
}
