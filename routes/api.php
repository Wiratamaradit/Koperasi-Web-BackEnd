<?php

use App\Http\Controllers\HistorytransController;
use App\Http\Controllers\LoantransController;
use App\Http\Controllers\SavingpaymentController;
use App\Http\Controllers\SystemconfigController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\SimulasiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('userLogin',[UserController::class,'userLogin']);
Route::post('userAdd',[UserController::class,'userAdd']);
Route::get('userList',[UserController::class,'userList']);
Route::post('userValidationRegion/{id}', [UserController::class, 'userValidationRegion']);
Route::post('userValidationGeneral/{id}', [UserController::class, 'userValidationGeneral']);

Route::post('loanAdd',[LoanController::class,'loanAdd']);
Route::get('loanList',[LoanController::class,'loanList']);
Route::post('loanValidationRegion/{id}', [LoanController::class, 'loanValidationRegion']);
Route::post('loanValidationGeneral/{id}', [LoanController::class, 'loanValidationGeneral']);

Route::post('saveAdd',[SavingController::class,'saveAdd']);
Route::get('saveList',[SavingController::class,'saveList']);
Route::post('saveValidationRegion/{id}', [SavingController::class, 'saveValidationRegion']);

Route::get('installList',[InstallmentController::class,'installList']);

Route::post('loantransAdd',[LoantransController::class,'loantransAdd']);
Route::get('loantransList',[LoantransController::class,'loantransList']);

Route::post('savepayAdd',[SavingpaymentController::class,'savepayAdd']);
Route::get('savepayList',[SavingpaymentController::class,'savepayList']);

Route::post('historyAdd',[HistorytransController::class,'historyAdd']);
Route::get('historyList',[HistorytransController::class,'historyList']);

Route::post('systemAdd',[SystemconfigController::class,'systemAdd']);
Route::get('systemList',[SystemconfigController::class,'systemList']);

Route::post('loanSimulation',[SimulasiController::class,'loanSimulation']);
Route::post('savingSimulation',[SimulasiController::class,'savingSimulation']);