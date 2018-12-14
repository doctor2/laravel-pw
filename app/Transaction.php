<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createTransaction($debit_user, $credit_user, $amount)
    {
        while($transaction_key = str_random(32)){
            if(!\App\Transaction::where('transaction_key', $transaction_key)->first()){
                break;
            }
        }
        
        $debit_user_balance = $debit_user->balance->fresh()->balance - $amount;
        $credit_user_balance = $credit_user->balance->fresh()->balance + $amount;
        
        $tr1 = static::create([
            'user_id' => $credit_user->id,
            'transaction_key' => $transaction_key,
            'amount' => $amount,
            'user_balance' => $credit_user_balance,
            'transaction_type' => 'CREDIT',
        ]);

        $tr2 = static::create([
            'user_id' => $debit_user->id,
            'transaction_key' => $transaction_key,
            'amount' => $amount,
            'user_balance' => $debit_user_balance,
            'transaction_type' => 'DEBIT',
        ]);
        
        if ($tr1 && $tr2) {
            $debit_user->balance()->update(['balance' => $debit_user_balance]);
            $credit_user->balance()->update(['balance' => $credit_user_balance]);
        }
    }
}
