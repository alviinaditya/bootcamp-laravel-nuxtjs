<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResponsibilityRequest;
use App\Models\Responsibility;
use Exception;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $responsibilityQuery = Responsibility::query();

        if ($id) {
            $responsibility = $responsibilityQuery->find($id);
            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility Found!');
            }
            return ResponseFormatter::error('Responsibility Not Found!', 404);
        }

        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
            if ($responsibilities->count() === 0) {
                return ResponseFormatter::error('Responsibility Not Found!', 404);
            }
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibility Found!'
        );
    }

    public function create(ResponsibilityRequest $request)
    {
        try {
            // Create Responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if (!$responsibility) {
                throw new Exception('Responsibility not Created');
            }

            return ResponseFormatter::success($responsibility, 'Responsibility Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $responsibility = Responsibility::find($id);
            if (!$responsibility) {
                throw new Exception('Responsibility not Found');
            }

            $responsibility->delete();

            return ResponseFormatter::success('Responsibility Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
