<?php

namespace Tests\Feature;

use Faker\Factory;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    // Create user and authenticate the user
    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        return $token;
    }

    /**
     * A basic feature test Loan application status.
     *
     * @return void
     */
    public function test_loan_application_status()
    {
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('api.loans'));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test Loan apply.
     *
     * @return void
     */
    public function test_loan_apply()
    {
        //Get token
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('api.applyLoan'), [
            'amount' => 100000,
            'yearly_term' => 4
        ]);
        $response->assertStatus(200);
    }
}
