<?php

namespace App\Services;

use App\TransactionAmount;
use App\TransactionCredit;
use App\TransactionDebit;

class AdminTransactionService
{
    public function getTransactionListQuery()
    {
        $query = \DB::table('transaction_debits as tr_d')
            ->select('tr.amount', 'tr.created_at', 'u.name as debit_user_name', 'user.name as crebit_user_name', 'tr.id as transaction_key')
            ->join('transaction_amounts as tr', 'tr_d.transaction_amounts_id', '=', 'tr.id')
            ->join('transaction_credits as tr_c', 'tr_c.transaction_amounts_id', '=', 'tr.id')
            ->join('users as user', 'tr_c.user_id', '=', 'user.id')
            ->join('users as u', 'tr_d.user_id', '=', 'u.id')
        ;
        return $query;
    }

    public function filterTransactionList($query)
    {
        if (!empty($value = request('date'))) {
            $query
                ->where('tr.created_at', 'like', '%' . request('date') . '%');
        }

        if (!empty($value = request('debit_user_name'))) {
            $query
                ->where('u.name', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('crebit_user_name'))) {
            $query
                ->where('user.name', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('amount'))) {
            $query
                ->where('tr.amount', 'like', '%' . $value . '%');
        }

    }

    public function orderTransactionList($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';
        $sort = request('sort');

        if ($sort == 'date') {
            $query->orderBy('tr.created_at', $order);

        } elseif ($sort == 'debit_user_name') {
            $query->orderBy('u.name', $order);

        } elseif ($sort == 'amount') {
            $query->orderBy('tr.amount', $order);

        } elseif ($sort == 'crebit_user_name') {
            $query->orderBy('user.name', $order);

        } else {
            $query->orderBy('tr.id', 'desc');

        }
    }

    public function getByKey($key)
    {
        $transaction = $this->getTransactionListQuery()
            ->addSelect('tr_c.user_balance as crebit_user_balance')
            ->addSelect('tr_d.user_balance as debit_user_balance')
            ->where('tr.id', $key)
            ->first();

        return $transaction ? collect($transaction)->toArray() : null;
    }

    public function update($key, $amount)
    {
        $transaction = TransactionAmount::where('id', $key)->first();

        if (empty($transaction)) {
            throw new \Exception('Not valid transaction key!');
        }

        \DB::transaction(function () use ($transaction, $amount) {

            $success = true;
            $oldAmount = $transaction->amount;
            $success &= $transaction->update([
                'amount' => $amount,
            ]);

            $tr1 = TransactionDebit::where('transaction_amounts_id', $transaction->id)->first();
            $success &= $tr1->update([
                'user_balance' => $tr1->user_balance + $oldAmount - $amount,
            ]);

            $tr2 = TransactionCredit::where('transaction_amounts_id', $transaction->id)->first();
            $success &= $tr2->update([
                'user_balance' => $tr2->user_balance + $amount - $oldAmount,
            ]);

            if (!$success) {
                throw new \Exception('Database error!');
            }
        });
    }
}
