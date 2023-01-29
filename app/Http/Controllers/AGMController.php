<?php

namespace App\Http\Controllers;

use App\Models\VotingItem;
use Exception;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\AGM;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddAGMRequest;


class AGMController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\AddAGMRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddAGMRequest $request)
    {

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $checkCompany = Company::where('user_id', auth()->user()->id)->first();

            if(is_null($checkCompany)){
                return $this->jsonErrorResponse("You don't have access to create AGM for this company",400);
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
            return $this->jsonErrorResponse("Something went wrong! " . $e->getMessage(),500);
        }

        return $this->jsonSuccessResponse("AGM added successfully",$agm, 201);
    }
}
