<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionCredit extends Model
{
    protected $guarded = [];

    public $timestamps = false; 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
