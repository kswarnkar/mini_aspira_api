<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RepaymentController extends Controller
{
    protected $user;

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
        $repayments = $this->user->repayments()->get();
        if (empty($repayments->toArray())) {
            return response()->json([
                'message' => 'we did not find any installment history!'
            ]);
        } else {
            return response()->json([
                'status' => true,
                'data' => $repayments->toArray()
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
        $validator = Validator::make($request->all(), [
            // 'total_paid_amount' => 'sometimes|required|regex:/^\d*(\.\d{2})?$/',
            'emi' => 'required|regex:/^\d*(\.\d{2})?$/',
            // 'remaining_period' => 'sometimes|required|integer'
        ]);

        // Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $repayments = $this->user->repayments()->get();
        $loans = $this->user->loans()->where('status', '=', 1)->get();

        // return response()->json($loans);
        if (!$repayments->isEmpty()) {
            $oldPayment = $repayments->last();
            $tpa    = $oldPayment['total_paid_amount'];
            $rp     = $oldPayment['remaining_period'];
            $time   = $oldPayment['week_day'];
        } else {
            $tpa    = 0;
            $rp     = $loans[0]['weekly_term'];
            $time   = $loans[0]['updated_at'];
        }

        // Request is valid, create new Loan
        $repayment = new Repayment();
        $repayment->total_paid_amount = $request->emi + $tpa;
        $repayment->emi = $request->emi;
        $repayment->week_day = Carbon::parse($time)->addWeek(1);
        $repayment->remaining_period = $rp - 1;

        if ($this->user->repayments()->save($repayment)) {
            return response()->json([
                'status' => "your installment paid successfully.",
                'repayment' => $repayment
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Oops, the repayment could not be saved"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Repayment  $repayment
     * @return \Illuminate\Http\Response
     */
    public function show(Repayment $repayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Repayment  $repayment
     * @return \Illuminate\Http\Response
     */
    public function edit(Repayment $repayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Repayment  $repayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Repayment $repayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Repayment  $repayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Repayment $repayment)
    {
        //
    }
}
