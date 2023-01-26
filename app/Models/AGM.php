<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AGM extends Model
{
    use HasFactory;

    protected $table = 'agms';

    protected $with = ['items'];

    protected $fillable = ["name","company_id" , "user_id", "date"];

    public function items()
    {
        return $this->hasMany(VotingItem::class, "id");
    }

}
