<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\User;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\VotingItem;
use App\Models\AGM;

class ShareholderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getItemsToVoteOn()
    {
        $shareholderCompanies = auth()->user()->shares()->pluck('company_id');
        $companies = AGM::where('company_id', $shareholderCompanies)->get();
        return response([
            'message' => 'List of items per agenda',
            'data' => $companies
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdminCompanies()
    {
        $companies = Company::where('user_id',auth()->user()->id)->get();
        return response([
            'message' => 'List of companies that belongs to an admin',
            'data' => $companies
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            $user->assignRole('shareholder');

            if($request->has("units") && $request->has("company_id")) {
                Share::create([
                    'user_id' => $user->id,
                    'units' => $request->get("units"),
                    'company_id' => $request->get("company_id"),
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response([
                'status' => 'Error',
                'message' => "Something went wrong! " . $e->getMessage()
            ], 500);
        }

        return response([
            'status' => 'Ok',
            'message' => 'Shareholder added successfully',
            'data' => $user
        ], 201);
    }
}
