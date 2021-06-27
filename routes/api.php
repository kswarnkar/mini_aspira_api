<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Route::middleware('auth:api')->get('/user', function (Request $request) { return $request->user(); });
*/

Route::post('login', [ApiController::class, 'authenticate'])->name('api.login');
Route::post('register', [ApiController::class, 'register'])->name('api.register');

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('logout', [ApiController::class, 'logout'])->name('api.logout');
    Route::get('get_user', [ApiController::class, 'get_user'])->name('api.user');
    Route::get('loans', [LoanController::class, 'index'])->name('api.loans');
    Route::post('create', [LoanController::class, 'store'])->name('api.applyLoan');
    Route::get('repayment', [RepaymentController::class, 'index'])->name('api.repayment');
    Route::post('repayCreate', [RepaymentController::class, 'store'])->name('api.submitRepay');
});
