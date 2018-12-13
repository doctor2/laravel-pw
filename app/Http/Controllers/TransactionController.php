<?php

namespace App\Http\Controllers;

use App\Rules\MaxUserBalance;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->expectsJson()) {
            $transactionsQuery = $this->getTransactionsListQuery(auth()->id());

            $this->filterTransactionList($transactionsQuery);

            $this->orderTransactionList($transactionsQuery);

            $transactions = $transactionsQuery // ->get();
            ->paginate(7);

            return $transactions;
        }

        return view('transactions.index');
    }

    public function create()
    {
        $transaction = [
            'user_name' => '',
            'user_id' => '',
            'amount' => '',
        ];

        if (request('key')) {
            $oldTransaction = \App\Transaction::where('transaction_key', request('key'))
                ->where('user_id', '!=', auth()->id())
                ->with('user')
                ->first();

            if ($oldTransaction) {
                $transaction['user_name'] = $oldTransaction->user->name;
                $transaction['user_id'] = $oldTransaction->user->id;
                $transaction['amount'] = $oldTransaction->amount;
            }
        }

        return view('transactions.create', compact('transaction'));
    }

    public function store(Transaction $transaction, MaxUserBalance $maxBalance)
    {
        request()->validate([
            'amount' => ['required', $maxBalance],
            'user_name' => 'required|exists:users,name',
        ]);

        $transaction->createTransaction(
            auth()->user(),
            \App\User::where('id', request('user_id'))->first(),
            request('amount'));

        return redirect()->route('transactions.index')
            ->with('success', 'Your thread has been published!');
    }

    public function getTransactionsListQuery($userId)
    {
        $transactionsQuery =
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.transaction_type', 'tr.transaction_key')
            ->where('tr.user_id', $userId)
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.user_id', '!=', $userId)
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
