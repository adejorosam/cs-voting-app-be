<?php

namespace App\Http\Controllers;

use App\Models\VotingItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotingItemController extends Controller
{
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddVotingItemRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'agm_id' => 'required|exists:agms,id',
        ]);

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
