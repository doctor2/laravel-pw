<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    public const DEBIT = 'DEBIT';
    
    public const CREDIT = 'CREDIT';

    protected $guarded = [];

    public $timestamps = false; 
}
