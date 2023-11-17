<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
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

// Departments route
Route::post("create-department", [DepartmentController::class, 'create']);
Route::get('/department-list', [DepartmentController::class, 'index']);
Route::put('/update-department/{id}', [DepartmentController::class, 'update']);
Route::delete('/delete-department/{id}', [DepartmentController::class, 'destroy']);

// Employees route
Route::post("create-employee", [EmployeeController::class, 'create']);
Route::get('/employees-list/{departmentId}', [EmployeeController::class, 'index']);
Route::get('/employee-details/{employeeId}', [EmployeeController::class, 'view']);
Route::post('/employee/contact-number-add/{employeeId}', [EmployeeController::class, 'addContactNumber']);
Route::post('/employee/add-address/{employeeId}', [EmployeeController::class, 'addAddress']);
Route::delete('/delete-employee/{employeeId}', [EmployeeController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
