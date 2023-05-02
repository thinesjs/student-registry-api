<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\StudentsController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// AUTH FOR STAFF
Route::controller(APIController::class)->group(function() {
    Route::post('login', 'userLogin');
    Route::post('register', 'userRegister');
});

// CRUD FOR STUDENTS DATA
Route::middleware('auth:api')->group( function () {
    Route::get('logout', [APIController::class, 'userLogout']);
    Route::resource('students', StudentsController::class);
});
