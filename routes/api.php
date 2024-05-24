<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/transaction',[TrackerController::class, 'store']);
Route::post('/transaction/update/{id}',[TrackerController::class, 'update']);
Route::post('/transaction/response',[TrackerController::class, 'response']);
Route::get('/transaction/response',[TrackerController::class, 'response_pay']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
