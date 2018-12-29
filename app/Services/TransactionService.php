<?php

namespace App\Services;

use App\Transaction;

class TransactionService
{
    public const CREDIT = "CREDIT";
    public const DEBIT = "DEBIT";
    public function create($debit_user, $credit_user, $amount)
    {
        \DB::transaction(function () use ($debit_user, $credit_user, $amount) {

            $debit_user_balance = $debit_user->balance->fresh()->balance - $amount;
            $credit_user_balance = $credit_user->balance->fresh()->balance + $amount;

            $tr = Transaction::create([
                'amount' => $amount,
                'debit_user_id' => $debit_user->id,
                'debit_user_balance' => $debit_user_balance,
                'credit_user_id' => $credit_user->id,
                'credit_user_balance' => $credit_user_balance,
            ]);

            if ($success = !empty($tr)) {
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

        $this->filterTransactions($debitQuery, self::DEBIT);

        $query = $this->getCreditTransactionsQuery($userId);

        $this->filterTransactions($query, self::CREDIT);

        $query->union($debitQuery);

        $this->orderTransactions($query);

        return $query;
    }

    public function getDebitTransactionsQuery($userId)
    {
        $query = \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.debit_user_balance as user_balance', 'tr.created_at', 'u_c.name as user_name', 'tr.id as transaction_key')
            ->addSelect(\DB::raw("'DEBIT' AS transaction_type"))
            ->where('tr.debit_user_id', $userId)
            ->join('users as u_c', 'tr.credit_user_id', '=', 'u_c.id');

        return $query;
    }

    public function getCreditTransactionsQuery($userId)
    {
        $query =
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.credit_user_balance as user_balance', 'tr.created_at', 'u_d.name as user_name', 'tr.id as transaction_key')
            ->addSelect(\DB::raw("'CREDIT' AS transaction_type"))
            ->where('tr.credit_user_id', $userId)
            ->join('users as u_d', 'tr.debit_user_id', '=', 'u_d.id');

        return $query;
    }

    public function filterTransactions($query, $transactionType)
    {
        if (!empty($value = request('date'))) {
            $query
                ->where('tr.created_at', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('user_balance'))) {

            $queryParam = $transactionType == self::DEBIT ? 'tr.debit_user_balance' : 'tr.credit_user_balance';

            $query->where($queryParam, 'like', '%' . $value . '%');
        }

        if (!empty($value = request('user_name'))) {

            $queryParam = $transactionType == self::DEBIT ? 'u_c.name' : 'u_d.name';

            $query->where($queryParam, 'like', '%' . $value . '%');
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
            $oldTransaction = \DB::table('transactions as tr')
                ->select('tr.amount', 'u_d.name as debit_user_name', 'u_c.name as credit_user_name', 'tr.credit_user_id', 'tr.debit_user_id')
                ->where('tr.id', $key)
                ->where(function ($query) use ($userId) {
                    $query->where('tr.credit_user_id', $userId)
                        ->orWhere('tr.debit_user_id', $userId);
                })
                ->join('users as u_c', 'tr.credit_user_id', '=', 'u_c.id')
                ->join('users as u_d', 'tr.debit_user_id', '=', 'u_d.id')
                ->first();

            if ($oldTransaction) {

                $transaction['amount'] = $oldTransaction->amount;
                if ($oldTransaction->debit_user_id == $userId) {
                    $transaction['user_name'] = $oldTransaction->credit_user_name;
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
