<?php

namespace App\Services;

use App\TransactionAmount;
use App\TransactionCredit;
use App\TransactionDebit;

class TransactionService
{
    public function create($debit_user, $credit_user, $amount)
    {
        \DB::transaction(function () use ($debit_user, $credit_user, $amount) {

            $debit_user_balance = $debit_user->balance->fresh()->balance - $amount;
            $credit_user_balance = $credit_user->balance->fresh()->balance + $amount;

            $transactionAmount = TransactionAmount::create([
                'amount' => $amount,
            ]);

            $tr1 = TransactionCredit::create([
                'user_id' => $credit_user->id,
                'user_balance' => $credit_user_balance,
                'transaction_amounts_id' => $transactionAmount->id,
            ]);

            $tr2 = TransactionDebit::create([
                'user_id' => $debit_user->id,
                'user_balance' => $debit_user_balance,
                'transaction_amounts_id' => $transactionAmount->id,
            ]);

            if ($success = $tr1 && $tr2) {
                $success &= $debit_user->balance()->update(['balance' => $debit_user_balance]);
                $success &= $credit_user->balance()->update(['balance' => $credit_user_balance]);
            }

            if (!$success) {
                throw new \Exception('Database error!');
            }
        });
    }

    public function getTransactionsQueryWithFilterAndOrder($userId)
    {
        $debitQuery = $this->getDebitTransactionsQuery($userId);

        $this->filterTransactions($debitQuery, 'tr_d');

        $query = $this->getCreditTransactionsQuery($userId);

        $this->filterTransactions($query, 'tr_c');

        $query->union($debitQuery);

        $this->orderTransactions($query);

        return $query;
    }

    public function getDebitTransactionsQuery($userId)
    {
        $query = \DB::table('transaction_debits as tr_d')
            ->select('tr.amount', 'tr_d.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.id as transaction_key')
            ->addSelect(\DB::raw("'DEBIT' AS transaction_type"))
            ->where('tr_d.user_id', $userId)
            ->join('transaction_amounts as tr', 'tr_d.transaction_amounts_id', '=', 'tr.id')
            ->join('transaction_credits as tr_c', 'tr_c.transaction_amounts_id', '=', 'tr.id')
            ->join('users as u', 'tr_c.user_id', '=', 'u.id');

        return $query;
    }

    public function getCreditTransactionsQuery($userId)
    {
        $query =
        \DB::table('transaction_credits as tr_c')
            ->select('tr.amount', 'tr_c.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.id as transaction_key')
            ->addSelect(\DB::raw("'CREDIT' AS transaction_type"))
            ->where('tr_c.user_id', $userId)
            ->join('transaction_amounts as tr', 'tr_c.transaction_amounts_id', '=', 'tr.id')
            ->join('transaction_debits as tr_d', 'tr_d.transaction_amounts_id', '=', 'tr.id')
            ->join('users as u', 'tr_d.user_id', '=', 'u.id');

        return $query;
    }

    public function filterTransactions($query, $table)
    {
        if (!empty($value = request('date'))) {
            $query
                ->where('tr.created_at', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('user_balance'))) {
            $query
                ->where($table . '.user_balance', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('amount'))) {
            $query
                ->where('tr.amount', 'like', '%' . $value . '%');
        }
    }

    public function orderTransactions($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';
        $sort = request('sort');

        if ($sort == 'date') {
            $query->orderBy('created_at', $order);

        } elseif ($sort == 'user_name') {
            $query->orderBy('user_name', $order);

        } elseif ($sort == 'amount') {
            $query->orderBy('amount', $order);

        } elseif ($sort == 'user_balance') {
            $query->orderBy('user_balance', $order);

        } else {
            $query->orderBy('transaction_key', 'desc');
        }
    }

    public function getByKeyForCurentUser($key)
    {
        $transaction = [
            'user_name' => '',
            'user_id' => '',
            'amount' => '',
        ];

        if ($key) {
            $userId = auth()->id();
            $oldTransaction = \DB::table('transaction_amounts as tr')
                ->select('tr.amount', 'u.name as debit_user_name', 'user.name as crebit_user_name', 'tr_c.user_id as credit_user_id', 'tr_d.user_id as debit_user_id')
                ->where('tr.id', $key)
                ->join('transaction_credits as tr_c', 'tr_c.transaction_amounts_id', '=', 'tr.id')
                ->join('transaction_debits as tr_d', 'tr_c.transaction_amounts_id', '=', 'tr.id')
                ->where(function ($query) use ($userId) {
                    $query->where('tr_c.user_id', $userId)
                        ->orWhere('tr_d.user_id', $userId);
                })
                ->join('users as user', 'tr_c.user_id', '=', 'user.id')
                ->join('users as u', 'tr_d.user_id', '=', 'u.id')
                ->first();

            if ($oldTransaction) {

                $transaction['amount'] = $oldTransaction->amount;
                if ($oldTransaction->debit_user_id == $userId) {
                    $transaction['user_name'] = $oldTransaction->crebit_user_name;
                    $transaction['user_id'] = $oldTransaction->credit_user_id;
                } else {
                    $transaction['user_name'] = $oldTransaction->debit_user_name;
                    $transaction['user_id'] = $oldTransaction->debit_user_id;
                }
            }
        }

        return $transaction;
    }
}
