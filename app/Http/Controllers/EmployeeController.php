<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function create(Request $request)
    {
        // Validator validates the input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees,email',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation errors as needed
            return response()->json(['status' => 422, 'message' => $validator->errors(), 'data' => []], 422);
        }

        // If the data is validated then adding into the table
        $validatedData = $validator->validated();
        $employee = Employee::create($validatedData);

        return response()->json(['status' => 201, 'message' => 'Employee created successfully', 'data' => $employee], 201);
    }

    public function index($departmentId)
    {
        // When Department id is 0 then show all the employees from all the departments
        if($departmentId == 0) {
            $employees = Employee::all();
        } else {
            // Show all employees department wise
            $employees = Employee::where('department_id', $departmentId)->get();
        }

        // If employees exist in a depratment then show the list
        if(count($employees) > 0) {
            return response()->json(['status' => 200, 'message' => 'List of all departments', 'data' => $employees], 200);
        } else {
            // No employees exist in a department
            return response()->json(['status' => 200, 'message' => 'No employees exist in this department', 'data' => $employees], 200);
        }
    }
}
