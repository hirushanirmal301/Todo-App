<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[TaskController::class,'index'])->name('task.index');
Route::post('/task/store',[TaskController::class,'store'])->name('task.store');
Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('task.status');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('task.destroy');


