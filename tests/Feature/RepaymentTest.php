<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RepaymentTest extends TestCase
{
    // use RefreshDatabase;

    // Create user and authenticate the user
    protected function authenticate()
    {
        $user = User::factory()->has(Loan::factory())->create();
        $token = JWTAuth::fromUser($user);
        $loan = $user->loans()->first()->update(['status' => 1]);
        return $token;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_repayment_status()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('api.repayment'));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_loan_repayment()
    {
        $token = $this->authenticate();
        $loan = Loan::where('status', '=', 1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('api.submitRepay'), [
            'total_paid_amount' => $loan->emi,  // 541.08,
            'emi' =>  $loan->emi,   // 541.08,
            'week_day' => Carbon::parse($loan->updated_at)->addWeek(1), // "2021-06-26 07:07:00",
            'remaining_period' => $loan->remaining_period,  // 203,
        ]);

        $response->assertStatus(200);
    }
}
