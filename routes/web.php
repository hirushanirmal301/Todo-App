<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/task',[TaskController::class,'index'])->name('task.index');
Route::post('/task/store',[TaskController::class,'store'])->name('task.store');

