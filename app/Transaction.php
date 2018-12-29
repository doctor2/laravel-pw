<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function debitUser()
    {
        return $this->belongsTo(User::class);
    }

    public function creditUser()
    {
        return $this->belongsto(user::class);
    }
}
