<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function create(Request $request)
    {
        // Validator validates the input data
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string|max:255|unique:departments,department_name',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation errors as needed
            return response()->json(['status' => 422, 'message' => $validator->errors(), 'data' => []], 422);
        }

        // If the data is validated then adding into the table
        $validatedData = $validator->validated();
        $department = Department::create($validatedData);

        return response()->json(['status' => 201, 'message' => 'Department created successfully', 'data' => $department], 201);
    }

    public function update(Request $request, $id)
    {
        // Validator validates the input data
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation errors as needed
            return response()->json(['status' => 422, 'message' => $validator->errors(), 'data' => []], 422);
        }

        $department = Department::find($id);

        if (!$department) {
            return response()->json(['status' => 404, 'message' => 'Department not found', 'data' => []], 404);
        }

        // If the data is validated, update the department name
        $validatedData = $validator->validated();
        $department->update(['department_name' => $validatedData['department_name']]);

        return response()->json(['status' => 200, 'message' => 'Department updated successfully', 'data' => $department], 200);
    }   

    public function destroy($id)
    {
        $department = Department::find($id);

        // When department doesn't exists
        if (!$department) {
            return response()->json(['status' => 404, 'message' => 'Department not found', 'data' => []], 404);
        }

        // Delete department
        $department->delete();
        return response()->json(['status' => 200, 'message' => 'Department deleted successfully', 'data' => []], 200);
    }

    public function index()
    {
        $departments = Department::all();
        return response()->json(['status' => 200, 'message' => 'List of all departments', 'data' => $departments], 200);
    }
}
