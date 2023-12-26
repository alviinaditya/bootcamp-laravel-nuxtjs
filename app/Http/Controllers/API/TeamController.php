<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\TeamRequest;
use Exception;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $teamQuery = Team::query();
        if ($id) {
            $team = $teamQuery->find($id);
            if ($team) {
                return ResponseFormatter::success($team, 'Team Found!');
            }
            return ResponseFormatter::error('Team Not Found!', 404);
        }

        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
            if ($teams->count() === 0) {
                return ResponseFormatter::error('Team Not Found!', 404);
            }
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams Found!'
        );
    }

    public function create(TeamRequest $request)
    {
        try {
            // Upload Icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }
            // Create Team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new Exception('Team not Created');
            }

            return ResponseFormatter::success($team, 'Team Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(TeamRequest $request, $id)
    {
        try {
            // Get Company
            $team = Team::find($id);

            // Check if team exist
            if (!$team) {
                throw new Exception('team not Found');
            }

            // Upload logo
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($team, 'Team Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $team = Team::find($id);
            if (!$team) {
                throw new Exception('team not Found');
            }

            $team->delete();

            return ResponseFormatter::success('Team Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
