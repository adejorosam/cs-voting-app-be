<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "units", "company_id"];

     /**
     * @return BelongsTo
     * @description get the user for the share.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
