<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "item_id",
        "user_id",
        "number_of_vote",
        "vote",
    ];
}
