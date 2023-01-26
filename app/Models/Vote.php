<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = ["item_id", "yes", "no"];

    public function item()
    {
        return $this->belongsTo(VotingItem::class, 'item_id');
    }
}
