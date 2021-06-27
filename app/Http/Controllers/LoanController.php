<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loan;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LoanController extends Controller
{
    protected $user;
    protected $percentage = 6;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = $this->user->loans()->get();  // ->where('status', '=', 1)
        $repayments = $this->user->repayments()->get();

        if (!$loans->isEmpty()) {
            if ($loans[0]['status'] !== 0 && !$repayments->isEmpty()) {
                $repay = $repayments->last();
                return response()->json([
                    'message' => 'your next week installment date is ' . Carbon::parse($repay['week_day'])->addWeek(1),
                    'next_installment_date' => Carbon::parse($repay['week_day'])->addWeek(1),
                    'details' => $repay
                ]);
            } else if ($loans[0]['status'] !== 0) {
                return response()->json([
                    'message' => 'congratulations your loan has been approved.',
                    'next_installment_date' => 'your next week installment date is ' . Carbon::parse($loans[0]['updated_at'])->addWeek(1),
                    'details' => $loans
                ]);
            } else {
                return response()->json([
                    'message' => "your loan approval is awaited",
                    'details' => $loans
                ]);
            }
        } else {
            return response()->json([
                'message' => 'now you can apply for the Loan!',
                'details' => ''
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('amount', 'yearly_term'); // 'percentage', 'weekly_term', 'emi'
        $validator = Validator::make($data, [
            'amount' => 'required|regex:/^\d*(\.\d{2})?$/',
            'yearly_term' => 'required|integer',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new loan
        $loan = $this->user->loans()->create([
            'amount' => $request->amount,
            'yearly_term' => $request->yearly_term,
            'percentage' => $this->percentage,
            'weekly_term' => $request->yearly_term * 52,
            'emi' => Loan::emi_calculator($request->amount, $this->percentage, $request->yearly_term),
        ]);

        //loan created, return success response
        if ($this->user->loans()->save($loan)) {
            return response()->json([
                'status' => true,
                'message' => "your loan application is successfully submitted",
                'loan' => $loan
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Oops, the loan could not be saved"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
