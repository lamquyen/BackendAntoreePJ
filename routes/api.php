<?php
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware(['auth:sanctum'])->group(function () {
   
Route::middleware('admin')->group(function () {
        Route::get('/users/getUser',[UserController::class,'getUser']); // get users list
        Route::delete('/users/{id}', [UserController::class, 'destroyUser']); // delete user
    });
Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'updateUser']);//update user by user

Route::middleware(['auth:sanctum', 'admin'])->put('/users/{id}', [UserController::class, 'updateUserByAdmin']);

Route::middleware('auth:sanctum')->post('/users/logout', [AuthController::class, 'logout']);


});
Route::post('/users', [UserController::class, 'CreateUser']);
Route::post('/users/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'showUser']);

