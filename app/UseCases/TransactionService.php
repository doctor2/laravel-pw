<?php

namespace App\UseCases;

use App\Transaction;


class TransactionService
{
    private $typeService;

    public function __construct(TransactionTypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    public function create($debit_user, $credit_user, $amount)
    {
        $typeService = $this->typeService;

        \DB::transaction(function () use($debit_user, $credit_user, $amount, $typeService) {
            
            while ($transaction_key = str_random(32)) {
                if (!\App\Transaction::where('transaction_key', $transaction_key)->first()) {
                    break;
                }
            }
    
            $debit_user_balance = $debit_user->balance->fresh()->balance - $amount;
            $credit_user_balance = $credit_user->balance->fresh()->balance + $amount;

            $tr1 = Transaction::create([
                'user_id' => $credit_user->id,
                'transaction_key' => $transaction_key,
                'amount' => $amount,
                'user_balance' => $credit_user_balance,
                'transaction_type_id' => $typeService->getCreditId(),
            ]);

            $tr2 = Transaction::create([
                'user_id' => $debit_user->id,
                'transaction_key' => $transaction_key,
                'amount' => $amount,
                'user_balance' => $debit_user_balance,
                'transaction_type_id' => $typeService->getDebitId(),
            ]);

            if ($success = $tr1 && $tr2) {
                 $success &= $debit_user->balance()->update(['balance' => $debit_user_balance]);
                 $success &= $credit_user->balance()->update(['balance' => $credit_user_balance]);
            }

            if(!$success){
                throw new \Exception('Database error!');
            }
        });
    }

    public function update($key, $amount)
    {
        $typeService = $this->typeService;  
        $transactions = Transaction::where('transaction_key', $key)->get();

        if ($transactions->isEmpty()) {
            throw new \Exception('Not valid transaction key!');
        }
        
        \DB::transaction(function () use($transactions, $amount, $typeService) {
            
            $success = true;
            $oldAmount = $transactions->first()->amount;

            foreach($transactions as $tr){
                if($tr->transaction_type_id == $typeService->getDebitId()){
                    $diffBalance = $oldAmount - $amount;
                    $exceptionMessage = 'Too mutch amount!';
                }elseif($tr->transaction_type_id == $typeService->getCreditId()) {
                    $diffBalance =  $amount - $oldAmount;
                    $exceptionMessage = 'Too small amount!';
                }

                $oldBalance = $tr->user_balance + $diffBalance;
                $newBalanse = $tr->user->balance->balance + $diffBalance;

                if($newBalanse < 0 || $oldBalance < 0){
                    throw new \Exception($exceptionMessage);
                }

                $success &= $tr->update([
                    'amount' => $amount,
                    'user_balance' => $oldBalance,
                ]);
                
                $success &= $tr->user->balance()->update(['balance' => $newBalanse]);
            }

            if(!$success){
                throw new \Exception('Database error!');
            }
        });
    }

    public function getByKey($key)
    {
        $transactions = Transaction::where('transaction_key', $key)->with('user')->get();

        $transaction = [];

        foreach ($transactions as $tr) {
            if ($tr->transaction_type_id == $this->typeService->getDebitId()) {
                $transaction['debit_user_name'] = $tr->user->name;
                $transaction['debit_user_balance'] = $tr->user_balance;
            } elseif($tr->transaction_type_id == $this->typeService->getCreditId()) {
                $transaction['crebit_user_name'] = $tr->user->name;
                $transaction['crebit_user_balance'] = $tr->user_balance;
            }
            $transaction['transaction_key'] = $tr->transaction_key;
            $transaction['amount'] = $tr->amount;
            $transaction['created_at'] = (string) $tr->created_at;
        }

        return $transaction;
    }
}
