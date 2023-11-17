<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use App\Models\ContactNumber;
use App\Models\Address;

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
            $employees = Employee::with('department')
                                ->with('addresses')
                                ->with('contactNumbers')
                                ->get();
        } else {
            // Show all employees department wise
            $employees = Employee::where('department_id', $departmentId)
                            ->with('department')
                            ->with('addresses')
                            ->with('contactNumbers')
                            ->get();
        }

        // If employees exist in a depratment then show the list
        if(count($employees) > 0) {
            return response()->json(['status' => 200, 'message' => 'List of all departments', 'data' => $employees], 200);
        } else {
            // No employees exist in a department
            return response()->json(['status' => 200, 'message' => 'No employees exist in this department', 'data' => $employees], 200);
        }
    }

    public function view($employeeId)
    {
        // Check if employee exist or not
        $employee = Employee::where('id', $employeeId)
                            ->with('department')
                            ->with('addresses')
                            ->with('contactNumbers')
                            ->first();
        
        // If employee doesn't exist then show no employee found
        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found', 'data' => []], 404);
        }

        // Show employee details
        return response()->json(['status' => 200, 'message' => 'Employee details', 'data' => $employee], 200);
    }

    public function addContactNumber(Request $request, $employeeId)
    {
        // Validator validates the input data
        $validator = Validator::make($request->all(), [
            'number' => 'required|max:255|regex:/^[0-9]{10,15}$/',
            'type' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation errors as needed
            return response()->json(['status' => 422, 'message' => $validator->errors(), 'data' => []], 422);
        }

        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found', 'data' => []], 404);
        }

        // If the data is validated then adding into the table
        $validatedData = $validator->validated();
        $validatedData['employee_id'] = $employeeId;
        $contactNumber = ContactNumber::create($validatedData);

        return response()->json(['status' => 200, 'message' => 'Contact number added successfully', 'data' => $contactNumber], 200);
    }

    public function addAddress(Request $request, $employeeId)
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found', 'data' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'address_type' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation errors as needed
            return response()->json(['status' => 422, 'message' => $validator->errors(), 'data' => []], 422);
        }

        $validatedData = $validator->validated();

        // Add employee_id to the data
        $validatedData['employee_id'] = $employeeId;
        $address = Address::create($validatedData);

        return response()->json(['status' => 200, 'message' => 'Address added successfully', 'data' => $address], 200);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        // If employee doeesn't exists
        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found', 'data' => []], 404);
        }

        // Delete associated addresses
        $employee->addresses()->delete();

        // Delete associated contact numbers
        $employee->contactNumbers()->delete();

        // Delete the employee
        $employee->delete();

        return response()->json(['status' => 200, 'message' => 'Employee deleted successfully', 'data' => []], 200);
    }

    public function update(Request $request, $employeeId)
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found', 'data' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees,email',
            'department_id' => 'required|exists:departments,id',
            'contact_numbers.*.id' => 'required|integer',
            'contact_numbers.*.number' => 'required|max:255|regex:/^[0-9]{10,15}$/',
            'contact_numbers.*.type' => 'required|string|max:255',
            'addresses.*.id' => 'required|integer',
            'addresses.*.address_type' => 'required|string|max:255',
            'addresses.*.address_line_1' => 'required|string|max:255',
            'addresses.*.address_line_2' => 'nullable|string|max:255',
            'addresses.*.city' => 'required|string|max:255',
            'addresses.*.state' => 'required|string|max:255',
            'addresses.*.zip_code' => 'required|string|max:10',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation errors as needed
            return response()->json(['status' => 422, 'message' => $validator->errors(), 'data' => []], 422);
        }

        $validatedData = $validator->validated();

        // Update phone numbers
        foreach ($validatedData['contact_numbers'] as $numberData) {
            $employee->contactNumbers()->updateOrCreate(
                [
                    'id' => $numberData['id']
                ],
                [
                    'number' => $numberData['number'], 
                    'type' => $numberData['type']
                ]
            );
        }

        // Update Address
        foreach ($validatedData['addresses'] as $addressData) {
            $employee->addresses()->updateOrCreate(
                [
                    'id' => $addressData['id']
                ],
                [
                    'address_type' => $addressData['address_type'],
                    'address_line_1' => $addressData['address_line_1'],
                    'address_line_2' => isset($addressData['address_line_2']),
                    'city' => $addressData['city'],
                    'state' => $addressData['state'],
                    'zip_code' => $addressData['zip_code'],
                ]
            );
        }

        // Get Updated Employee Data
        $employee = Employee::where('id', $employeeId)
                            ->with('department')
                            ->with('addresses')
                            ->with('contactNumbers')
                            ->first();      
                              
        return response()->json(['status' => 200, 'message' => 'Contact numbers updated successfully', 'data' => $employee], 200);
    }
}
