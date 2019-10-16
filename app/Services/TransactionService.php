<?php

namespace App\Services;

use App\Transaction;

class TransactionService
{
    public const CREDIT = "CREDIT";
    public const DEBIT = "DEBIT";

    /**
     * @param $debit_user - debit user object
     * @param $credit_user - credit user object
     * @param int $amount - amount
     */
    public function create($debit_user, $credit_user, int $amount)
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

    /**
     * @param int $userId
     * @return mixed
     */
    public function getTransactionsQueryWithFilter(int $userId)
    {
        $debitQuery = $this->getDebitTransactionsQuery($userId);

        $this->filterTransactions($debitQuery, self::DEBIT);

        $query = $this->getCreditTransactionsQuery($userId);

        $this->filterTransactions($query, self::CREDIT);

        $query->union($debitQuery);

        return $query;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    protected function getDebitTransactionsQuery(int $userId)
    {
        $query = \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.debit_user_balance as user_balance', 'tr.created_at', 'u_c.name as user_name', 'tr.id')
            ->addSelect(\DB::raw("'DEBIT' AS transaction_type"))
            ->where('tr.debit_user_id', $userId)
            ->join('users as u_c', 'tr.credit_user_id', '=', 'u_c.id');

        return $query;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    protected function getCreditTransactionsQuery(int $userId)
    {
        $query =
            \DB::table('transactions as tr')
                ->select('tr.amount', 'tr.credit_user_balance as user_balance', 'tr.created_at', 'u_d.name as user_name', 'tr.id')
                ->addSelect(\DB::raw("'CREDIT' AS transaction_type"))
                ->where('tr.credit_user_id', $userId)
                ->join('users as u_d', 'tr.debit_user_id', '=', 'u_d.id');

        return $query;
    }

    /**
     * @param $query
     * @param $transactionType - DEBIT/CREDIT
     */
    protected function filterTransactions($query, $transactionType)
    {
        $q = request('search');
        if (empty($q['value']) || empty($q = trim($q['value']))) {
            return;
        }

        $query->where(function ($queryLocal) use ($q, $transactionType) {

            $queryLocal
                ->orWhere('tr.created_at', 'like', '%' . $q . '%');

            $queryParam = $transactionType == self::DEBIT ? 'tr.debit_user_balance' : 'tr.credit_user_balance';
            $queryLocal
                ->orWhere($queryParam, 'like', '%' . $q . '%');

            $queryParam = $transactionType == self::DEBIT ? 'u_c.name' : 'u_d.name';
            $queryLocal
                ->orWhere($queryParam, 'like', '%' . $q . '%');

            $queryLocal
                ->orWhere('tr.amount', 'like', '%' . $q . '%');
        });
    }

    /**
     * @param $query
     */
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
            $query->orderBy('id', 'desc');
        }
    }

    /**
     * @param int $id - transaction id
     * @param int $userId - user id
     * @return mixed - amount, debit_user_name, credit_user_name, credit_user_id, debit_user_id
     */
    public function getByIdAndUserId(int $id, int $userId)
    {
        $transaction = \DB::table('transactions as tr')
            ->select('tr.amount', 'u_d.name as debit_user_name', 'u_c.name as credit_user_name', 'tr.credit_user_id', 'tr.debit_user_id')
            ->where('tr.id', $id)
            ->where(function ($query) use ($userId) {
                $query->where('tr.credit_user_id', $userId)
                    ->orWhere('tr.debit_user_id', $userId);
            })
            ->join('users as u_c', 'tr.credit_user_id', '=', 'u_c.id')
            ->join('users as u_d', 'tr.debit_user_id', '=', 'u_d.id')
            ->first();

        return $transaction;
    }
}
