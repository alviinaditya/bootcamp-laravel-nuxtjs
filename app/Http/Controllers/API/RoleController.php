<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Exception;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilies = $request->input('with_responsibilies', false);

        $roleQuery = Role::query();
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);
            if ($role) {
                return ResponseFormatter::success($role, 'Role Found!');
            }
            return ResponseFormatter::error('Role Not Found!', 404);
        }

        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
            if ($roles->count() === 0) {
                return ResponseFormatter::error('Role Not Found!', 404);
            }
        }

        if ($with_responsibilies) {
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles Found!'
        );
    }

    public function create(RoleRequest $request)
    {
        try {
            // Create Role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception('Role not Created');
            }

            return ResponseFormatter::success($role, 'Role Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(RoleRequest $request, $id)
    {
        try {
            // Get Company
            $role = Role::find($id);

            // Check if role exist
            if (!$role) {
                throw new Exception('Role not Found');
            }

            // Update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($role, 'Role Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                throw new Exception('Role not Found');
            }

            $role->delete();

            return ResponseFormatter::success('Role Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
