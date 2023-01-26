<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingItem extends Model
{
    use HasFactory;

    protected $fillable = ["name", "agm_id"];

    public function vote()
    {
        return $this->hasOne(Vote::class, 'item_id');
    }

    public function agm()
    {
        return $this->belongsTo(AGM::class);
    }
}
