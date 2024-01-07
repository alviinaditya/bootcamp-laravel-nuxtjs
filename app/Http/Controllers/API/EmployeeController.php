<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Exception;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $gender = $request->input('gender');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $limit = $request->input('limit', 10);

        $employeeQuery = Employee::query();

        if ($id) {
            $employee = $employeeQuery->with(['team', 'role'])->find($id);
            if ($employee) {
                return ResponseFormatter::success($employee, 'Employee Found!');
            }
            return ResponseFormatter::error('Employee Not Found!', 404);
        }

        $employees = $employeeQuery;

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }

        if ($email) {
            $employees->where('email', $email);
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }

        if ($age) {
            $employees->where('age', $age);
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }

        if ($gender) {
            $employees->where('gender', $gender);
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }


        if ($phone) {
            $employees->where('phone', 'like', '%' . $phone . '%');
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }

        if ($team_id) {
            $employees->where('team_id', $team_id);
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }

        if ($role_id) {
            $employees->where('role_id', $role_id);
            if ($employees->count() === 0) {
                return ResponseFormatter::error('Employee Not Found!', 404);
            }
        }

        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employees Found!'
        );
    }

    public function create(EmployeeRequest $request)
    {
        try {
            // Upload Icon
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }
            // Create Employee
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            if (!$employee) {
                throw new Exception('Employee not Created');
            }

            return ResponseFormatter::success($employee, 'Employee Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(EmployeeRequest $request, $id)
    {
        try {
            // Get Company
            $employee = Employee::find($id);

            // Check if employee exist
            if (!$employee) {
                throw new Exception('employee not Found');
            }

            // Upload logo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Update employee
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $employee->photo,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            return ResponseFormatter::success($employee, 'Employee Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);
            if (!$employee) {
                throw new Exception('employee not Found');
            }

            $employee->delete();

            return ResponseFormatter::success('Employee Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
