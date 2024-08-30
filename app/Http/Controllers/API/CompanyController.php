<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function ($q) {
            $q->where('user_id', Auth::id());
        });
        if ($id) {
            $company = $companyQuery->find($id);
            if ($company) {
                return ResponseFormatter::success($company, 'Company Found!');
            }
            return ResponseFormatter::error('Company Not Found!', 404);
        }

        $companies = $companyQuery;

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
            if ($companies->count() === 0) {
                return ResponseFormatter::error('Company Not Found!', 404);
            }
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies Found!'
        );
    }

    public function create(CompanyRequest $request)
    {
        try {
            // Upload Logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }
            // Create Company
            $company = Company::create([
                'name' => $request->name,
                'logo' => isset($path) ? $path : '',
            ]);

            if (!$company) {
                throw new Exception('Company not Created');
            }

            // Attach Company to User
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // Load users at company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(CompanyRequest $request, $id)
    {
        try {
            // Get Company
            $company = Company::find($id);

            // Check if company exist
            if (!$company) {
                throw new Exception('Company not Found');
            }

            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // Update Company
            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo,
            ]);

            return ResponseFormatter::success($company, 'Company Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
