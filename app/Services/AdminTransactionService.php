<?php

namespace App\Services;

use App\Transaction;
use Exception;


class AdminTransactionService
{
    /**
     * Get transaction list
     *
     * @return mixed
     */
    public function getTransactionListQuery()
    {
        $query = \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.created_at', 'u_d.name as debit_user_name', 'u_c.name as credit_user_name', 'tr.id')
            ->join('users as u_c', 'tr.credit_user_id', '=', 'u_c.id')
            ->join('users as u_d', 'tr.debit_user_id', '=', 'u_d.id');
        return $query;
    }

    /**
     * @param $query
     */
    public function filterTransactionList($query)
    {
        if (!empty($value = request('created_at'))) {
            $query
                ->where('tr.created_at', 'like', '%' . request('date') . '%');
        }

        if (!empty($value = request('debit_user_name'))) {
            $query
                ->where('u_d.name', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('credit_user_name'))) {
            $query
                ->where('u_c.name', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('amount'))) {
            $query
                ->where('tr.amount', 'like', '%' . $value . '%');
        }

    }

    /**
     * @param $query
     */
    public function orderTransactionList($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';
        $sort = request('sort');

        if ($sort == 'date') {
            $query->orderBy('tr.created_at', $order);

        } elseif ($sort == 'debit_user_name') {
            $query->orderBy('u_d.name', $order);

        } elseif ($sort == 'amount') {
            $query->orderBy('tr.amount', $order);

        } elseif ($sort == 'credit_user_name') {
            $query->orderBy('u_c.name', $order);

        } else {
            $query->orderBy('tr.id', 'desc');

        }
    }

    /**
     * @param int $id - transaction id
     * @return mixed
     */
    public function getById(int $id)
    {
        $transaction = $this->getTransactionListQuery()
            ->addSelect('tr.credit_user_balance')
            ->addSelect('tr.debit_user_balance')
            ->where('tr.id', $id)
            ->first();

        return $transaction;
    }

    /**
     * Change users balances and make transaction
     *
     * @param int $id - transaction id
     * @param int $amount - amount
     * @throws Exception
     */
    public function update(int $id, int $amount)
    {
        $transaction = Transaction::where('id', $id)->first();

        if (empty($transaction)) {
            throw new Exception('Not valid transaction key!');
        }

        \DB::transaction(function () use ($transaction, $amount) {

            $success = $this->updateDebitUserBalance($transaction, $amount);

            $success &= $this->updateCreditUserBalance($transaction, $amount);

            $success &= $transaction->update([
                'amount' => $amount,
                'debit_user_balance' => $transaction->debitUser->balance->fresh()->balance,
                'credit_user_balance' => $transaction->creditUser->balance->fresh()->balance
            ]);

            if (!$success) {
                throw new Exception('Database error!');
            }
        });
    }

    /**
     * @param $transaction - transaction object
     * @param $amount  - amount
     *
     * @return mixed
     * @throws Exception
     */
    public function updateDebitUserBalance($transaction, $amount)
    {
        $oldAmount = $transaction->amount;

        $newDebitUserBalance = $transaction->debit_user_balance + $oldAmount - $amount;
        $newUserBalance = $transaction->debitUser->balance->balance + $oldAmount - $amount;

        if ($newDebitUserBalance < 0 || $newUserBalance < 0) {
            throw new Exception('Too large amount!');
        }

        return $transaction->debitUser->balance()->update(['balance' => $newUserBalance]);
    }

    /**
     * @param $transaction - transaction object
     * @param $amount  - amount
     *
     * @return mixed
     * @throws Exception
     */
    public function updateCreditUserBalance($transaction, $amount)
    {
        $oldAmount = $transaction->amount;

        $newCreditUserBalance = $transaction->credit_user_balance + $amount - $oldAmount;
        $newUserBalance = $transaction->creditUser->balance->balance + $amount - $oldAmount;

        if ($newCreditUserBalance < 0 || $newUserBalance < 0) {
            throw new Exception('Too small amount!');
        }

        return $transaction->creditUser->balance()->update(['balance' => $newUserBalance]);
    }
}
