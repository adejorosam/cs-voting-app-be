<?php

namespace App\Http\Controllers;

use App\Models\VotingItem;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests\AddVotingItemRequest;
use Illuminate\Support\Facades\DB;

class VotingItemController extends Controller
{
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\AddVotingItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddVotingItemRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $votingItem = VotingItem::create([
                'name' => $validated['name'],
                'agm_id' => $validated['agm_id'],
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
            'message' => 'Voting item created successfully',
            'data' => $votingItem
        ], 201);
    }
}
