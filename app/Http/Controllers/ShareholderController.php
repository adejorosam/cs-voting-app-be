<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\User;
use App\Models\Company;
use Exception;
use App\Http\Requests\AddCompanyRequest;
use App\Http\Requests\AddShareholderRequest;
use App\Http\Requests\AddUserToCompanyRequest;
use Illuminate\Support\Str;
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
        $agmItems = AGM::where('company_id', $shareholderCompanies)->get();
        return response([
            'message' => 'List of items per agenda',
            'data' => $agmItems
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
    public function store(AddShareholderRequest $request)
    {
        $validated = $request->validate();

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addCompany(AddCompanyRequest $request)
    {
        $validated = $request->validate();

        DB::beginTransaction();
        try {
            $company = Company::create([
                'name' => $validated['name'],
                'acronym' => Str::slug($validated['name']),
                'user_id' => auth()->user()->id
            ]);

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
            'message' => 'Company added successfully',
            'data' => $company
        ], 201);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addUserToCompany(AddUserToCompanyRequest $request)
    {
        $validated = $request->validate();

        DB::beginTransaction();
        try {
            $shareExist = Share::where(['user_id'=>$validated['user_id'], 'company_id'=>$validated['company_id']])->first();
            if(!is_null($shareExist)){
                return response([
                    'status' => 'Error',
                    'message' => "A record for this user exists already."
                ], 400);
            }
            $userExist = User::where('id', $validated['user_id'])->first();
            if(is_null($userExist)){
                return response([
                    'status' => 'Error',
                    'message' => "A record for this user does not exist."
                ], 400);
            }
            $share = Share::create([
                'units' => $validated['units'],
                'company_id' => $validated['company_id'],
                'user_id' => $validated['user_id']
            ]);

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
            'message' => 'User added to a company successfully',
            'data' => $share
        ], 201);
    }
}
