<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    
    public function index()
    {
        if (request()->expectsJson()) {
            $transactionsQuery = $this->getTransactionsListQuery();

            $this->filterTransactionList($transactionsQuery);

            $this->orderTransactionList($transactionsQuery);

            $transactions = $transactionsQuery // ->get();
            ->paginate(7);

            return $transactions;
        }

        return view('admin.transactions.index');
    }

    public function getTransactionsListQuery()
    {
        $transactionsQuery =
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.transaction_type', 'tr.transaction_key')
            ->where('tr.transaction_type', config('transaction.types.debit'))
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.transaction_type', config('transaction.types.credit'))
            ->join('users as u', 'trans.user_id', '=', 'u.id')
        ;
        return $transactionsQuery;
    }

    public function filterTransactionList($transactionsQuery)
    {
        if (request('date')) {
            $transactionsQuery
                ->where('tr.created_at', 'like', '%' . request('date') . '%');
        }

        if (request('user_name')) {
            $transactionsQuery
                ->where('u.name', 'like', '%' . request('user_name') . '%');
        }

        if (request('amount')) {
            $transactionsQuery
                ->where('tr.amount', request('amount'));
        }

        if (request('user_balance')) {
            $transactionsQuery
                ->where('tr.user_balance', request('user_balance'));
        }

        return $transactionsQuery;
    }

    public function orderTransactionList($transactionsQuery)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';

        if (request('sort') == 'date') {
            $transactionsQuery->orderBy('tr.created_at', $order);

        } elseif (request('sort') == 'user_name') {
            $transactionsQuery->orderBy('u.name', $order);
            
        } elseif (request('sort') == 'amount') {
            $transactionsQuery->orderBy('tr.amount', $order);

        } elseif (request('sort') == 'user_balance') {
            $transactionsQuery->orderBy('tr.user_balance', $order);

        } else {
            $transactionsQuery->orderBy('tr.id', 'desc');

        }
    }
}
