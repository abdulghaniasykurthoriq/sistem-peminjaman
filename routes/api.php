<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/items', [ItemController::class, 'index']);
});
// Route::group(['middleware' => 'auth:api'], function () {
//     Route::get('/items', function () {
//         return response()->json([
//             'message' => 'item q',
//         ]);
//     });
// });
