<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeRecordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//login画面の表示
Route::get('/login', [AuthController::class, "showLogin"])->name("showLogin");
//loginの処理
Route::post('/login', [AuthController::class, "login"])->name("login");
//logout
Route::post('/logout',[AuthController::class, "logout"])->name("logout");

//register画面の表示
Route::get('/register',[AuthController::class,"showRegister"])->name("showRegister");
//register処理
Route::post('/register',[AuthController::class,"register"])->name("register");

//打刻画面の表示
Route::get('/', [TimeRecordController::class, 'showHome'])->name("showHome");
//打刻処理
Route::middleware(['auth'])->group(function () {
    Route::post('/start-work', [TimeRecordController::class, 'startWork'])->name('start-work');
    Route::post('/end-work', [TimeRecordController::class, 'endWork'])->name('end-work');
    Route::post('/start-break', [TimeRecordController::class, 'startBreak'])->name('start-break');
    Route::post('/end-break', [TimeRecordController::class, 'endBreak'])->name('end-break');
});

//attendance画面の表示
Route::get('/attendance', [TimeRecordController::class, 'manage'])->name('manage');
Route::post('/attendance',[TimeRecordController::class,"showAttendance"])->name('showAttendance');







