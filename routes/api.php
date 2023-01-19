<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth', [AuthController::class, 'auth']);
Route::post('register', [AuthController::class, 'register']);

Route::prefix('news')->middleware('jwt.verify')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/{id}', [NewsController::class, 'show']);
    Route::post('/insert', [NewsController::class, 'add']);
    Route::delete('/delete/{id}', [NewsController::class, 'destroy']);
    Route::post('/update/{id}', [NewsController::class, 'update']);
});
