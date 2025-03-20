<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PropertiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/login/google', [LoginController::class, 'googleLogin']);
Route::post('/register', [LoginController::class, 'register']);
Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');

Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'reset']);
Route::get('/data_property', [PropertiController::class, 'data_property']);
Route::get('/data_property/{slug}', [PropertiController::class, 'show_data_property']);

Route::post('/booking', [OrderController::class, 'booking']);
Route::get('/booking/{kode_pemesanan}', [OrderController::class, 'get_order']);
Route::post('/invoiceCreate', [OrderController::class, 'invoiceCreate']);
Route::get('/invoice/{no_invoice}', [OrderController::class, 'get_invoice']);
Route::post('/midtrans-callback', [OrderController::class, 'handleMidtransCallback']);


Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index'])->middleware('isAdmin');

    // profile
    Route::get('/profile', [ProfilController::class, 'profile']);
    Route::put('/profile', [ProfilController::class, 'update']);

    // Category
    Route::get('/category', [CategoryController::class, 'index']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    // Properti
    Route::get('/property', [PropertiController::class, 'index']);
    Route::get('/property/{id}', [PropertiController::class, 'show']);
    Route::post('/property', [PropertiController::class, 'store']);
    Route::put('/property/{id}', [PropertiController::class, 'update']);
    Route::delete('/property/{id}', [PropertiController::class, 'destroy']);

    // Unit
    Route::get('/units', [PropertiController::class, 'getUnits']);
    Route::get('/units/{id}', [PropertiController::class, 'unitShow']);
    Route::post('/units', [PropertiController::class, 'storeUnit']);
    Route::put('/units/{id}', [PropertiController::class, 'updateUnit']);
    Route::delete('/units/{id}', [PropertiController::class, 'destroyUnit']);


    Route::get('/valueCategory', [ValueController::class, 'value_category']);
    Route::get('/valueProperty', [ValueController::class, 'value_property']);
});
