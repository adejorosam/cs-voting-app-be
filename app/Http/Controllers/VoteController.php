<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoteResource;
use App\Models\Share;
use App\Models\VoteLog;
use Exception;
use Illuminate\Http\AddVoteRequest;
use Illuminate\Http\GetMetricsRequest;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\throwException;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsefulMetrics(GetMetricsRequest $request)
    {
        $validated = $request->validate();

        $yesVotes = VoteLog::where(['vote'=> 'yes', 'item_id'=>$validated['item_id']])->sum('number_of_vote');
        $noVotes = VoteLog::where(['vote'=>'no', 'item_id'=>$validated['item_id']])->sum('number_of_vote');
        $totalVotes = VoteLog::where('item_id',$validated['item_id'])->count();
        return response([
            'status' => 'Ok',
            'message' => 'Useful metrics',
            'data' => [['yes votes'=>$yesVotes], ['no votes'=>$noVotes], ['totalVotes'=>$totalVotes]]
        ], 201);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddVoteRequest $request)
    {
        $validated = $request->validate();

        DB::beginTransaction();
        try {
            // Make sure value is yes or no
            if (!in_array($request->get('vote'), ['yes', 'no'])) {
                throw new Exception("Vote yes or no.");
            }

            //check if he/owns share
            $shareExist = Share::where(['user_id'=>auth()->user()->id], ['company_id'=>$validated['company_id']])->first();
            if(is_null($shareExist)){
                return response([
                    'status' => 'Error',
                    'message' => "You do not own a share in this company"
                ], 400);
            }

            // Check if shareholder has voted before
            $previousVote = VoteLog::where([
                ["item_id", $request->get('item_id')], 
                ["user_id", auth()->user()->id]]
            )->first();
            
            if (!is_null($previousVote)) {
                return response([
                    'status' => 'Error',
                    'message' => "You have voted already"
                ], 400);
            }
            
            $numberOfVotes = Share::where(['user_id'=>auth()->user()->id, 'company_id'=>$validated['company_id']])->first()->units;
         
            VoteLog::create([
                'item_id' => $request->get('item_id'),
                'user_id' => auth()->user()->id,
                'number_of_vote' => $numberOfVotes,
                'vote' => $request->get('vote'),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response([
                'status' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response([
            'status' => 'Ok',
            'message' => 'You have successfully casted your vote!'
        ], 201);
    }
}
