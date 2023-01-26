<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoteResource;
use App\Models\Share;
use App\Models\Vote;
use App\Models\VoteLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\throwException;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsefulMetrics(Request $request)
    {
        $validated = $request->validate([
            "item_id" => 'required|exists:voting_items,id',
        ]);

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            "vote" => 'required',
            "item_id" => 'required|exists:voting_items,id',
        ]);

        DB::beginTransaction();
        try {
            // Make sure value is yes or no
            if (!in_array($request->get('vote'), ['yes', 'no'])) {
                throw new Exception("Vote yes or no.");
            }

            // Check if shareholder has voted before
            $previousVote = VoteLog::where([
                ["item_id", $request->get('item_id')], 
                ["user_id", auth()->user()->id]]
            )->first();
            
            if (!is_null($previousVote)) {
                throw new Exception("You have voted already!");
            }
            
            $numberOfVotes = Share::where("user_id", auth()->user()->id)->get()->sum('units');
            $vote = Vote::where('item_id', $request->get('item_id'))->first();

            if (is_null($vote)) {
                Vote::create([
                    'item_id' => $request->get('item_id'),
                    'yes' => $request->get('vote') == "yes" ? $numberOfVotes : 0,
                    'no' => $request->get('vote') == "no" ? $numberOfVotes : 0,
                ]);
            } else {
                $vote->update([
                    'yes' => $request->get('vote') == "yes" ? $vote->yes + $numberOfVotes : $vote->yes,
                    'no' => $request->get('vote') == "no" ? $vote->no + $numberOfVotes : $vote->no
                ]);
            }

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
