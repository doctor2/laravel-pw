<?php

namespace App\Services;

use App\Transaction;


class AdminTransactionService
{
    public function getTransactionListQuery()
    {
        $query = \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.created_at', 'u_d.name as debit_user_name', 'u_c.name as credit_user_name', 'tr.id')
            ->join('users as u_c', 'tr.credit_user_id', '=', 'u_c.id')
            ->join('users as u_d', 'tr.debit_user_id', '=', 'u_d.id');
        return $query;
    }

    public function filterTransactionList($query)
    {
        $q = request('search');
        if(empty($q['value']) || empty($q = trim($q['value']))){
            return;
        }

        $query
            ->orWhere('tr.created_at', 'like', '%' . $q . '%');

        $query
            ->orWhere('u_d.name', 'like', '%' . $q . '%');

        $query
            ->orWhere('u_c.name', 'like', '%' . $q . '%');

        $query
            ->orWhere('tr.amount', 'like', '%' . $q . '%');

    }

    public function orderTransactionList($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';
        $sort = request('sort');

        if ($sort == 'date') {
            $query->orderBy('tr.created_at', $order);

        } elseif ($sort == 'debit_user_name') {
            $query->orderBy('u_d.name', $order);

        } elseif (request('amount')) {
//        } elseif ($sort == 'amount') {
            $query->orderBy('tr.amount', $order);

        } elseif ($sort == 'credit_user_name') {
            $query->orderBy('u_c.name', $order);

        } else {
            $query->orderBy('tr.id', 'desc');

        }
    }

    public function getById($id)
    {
        $transaction = $this->getTransactionListQuery()
            ->addSelect('tr.credit_user_balance')
            ->addSelect('tr.debit_user_balance')
            ->where('tr.id', $id)
            ->first();

        return $transaction;
    }

    public function update($id, $amount)
    {
        $transaction = Transaction::where('id', $id)->first();

        if (empty($transaction)) {
            throw new \Exception('Not valid transaction key!');
        }

        \DB::transaction(function () use ($transaction, $amount) {

            $oldAmount = $transaction->amount;

            // Изменения баланса дебита
            $newDebitUserBalance = $transaction->debit_user_balance + $oldAmount - $amount;
            $newUserBalanse = $transaction->debitUser->balance->balance + $oldAmount - $amount;

            if ($newDebitUserBalance < 0 || $newUserBalanse < 0) {
                throw new \Exception('Too large amount!');
            }
            $success = $transaction->debitUser->balance()->update(['balance' => $newUserBalanse]);

            // Изменения баланса кредита
            $newCreditUserBalance = $transaction->credit_user_balance + $amount - $oldAmount;
            $newUserBalanse = $transaction->creditUser->balance->balance + $amount - $oldAmount;

            if ($newCreditUserBalance < 0 || $newUserBalanse < 0) {
                throw new \Exception('Too small amount!');
            }
            $success &= $transaction->creditUser->balance()->update(['balance' => $newUserBalanse]);


            $success &= $transaction->update([
                'amount' => $amount,
                'debit_user_balance' => $newDebitUserBalance,
                'credit_user_balance' => $newCreditUserBalance
            ]);

            if (!$success) {
                throw new \Exception('Database error!');
            }
        });
    }
}
