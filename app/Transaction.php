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
        \DB::transaction(function () use($debit_user, $credit_user, $amount) {
            
            while ($transaction_key = str_random(32)) {
                if (!\App\Transaction::where('transaction_key', $transaction_key)->first()) {
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
                'transaction_type' => config('transaction.types.credit'),
            ]);

            $tr2 = static::create([
                'user_id' => $debit_user->id,
                'transaction_key' => $transaction_key,
                'amount' => $amount,
                'user_balance' => $debit_user_balance,
                'transaction_type' => config('transaction.types.debit'),
            ]);

            if ($success = $tr1 && $tr2) {
                 $success &= $debit_user->balance()->update(['balance' => $debit_user_balance]);
                 $success &= $credit_user->balance()->update(['balance' => $credit_user_balance]);
            }

            if(!$success){
                throw new \Exception('Database error');
            }
        });
    }
}
