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
}
