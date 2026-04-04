<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource("posts", PostController::class);

Route::post("/register", [App\Http\Controllers\AuthController::class, 'Register']);
Route::post("/login", [App\Http\Controllers\AuthController::class, 'Login']);
Route::post("/logout", [App\Http\Controllers\AuthController::class, 'Logout'])->middleware('auth:sanctum');

Route::post("/login-with-otp", [App\Http\Controllers\OtpController::class, 'loginWithOTP']);
Route::post("/verify-otp", [App\Http\Controllers\OtpController::class, 'verifyOTP']);

// Route::get("/posts", function () {
//     return 'API is working';
// });  