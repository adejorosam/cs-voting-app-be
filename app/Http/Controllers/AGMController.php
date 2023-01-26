<?php

namespace App\Http\Controllers;

use App\Models\VotingItem;
use Exception;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\AGM;
use Illuminate\Support\Facades\DB;

class AGMController extends Controller
{

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $todayDate = date('m/d/Y');
        $validated = $request->validate([
            'name' => 'required',
            'company_id' => 'required|exists:companies,id',
            'date' => 'required|date_format:Y-m-d|after:'.$todayDate
        ]);

        DB::beginTransaction();
        try {
            $checkCompany = Company::where('user_id', auth()->user()->id)->get();

            if(is_null($checkCompany)){
                return response([
                    'status' => 'Error',
                    'message' => "You don't have access to create AGM for this company"
                ], 200);
            }

            $agm = AGM::create([
                'name' => $validated['name'],
                'company_id' => $validated['company_id'],
                'user_id' => auth()->user()->id,
                'date' => $validated['date']
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
            'message' => 'AGM added successfully',
            'data' => $agm
        ], 201);
    }
}
