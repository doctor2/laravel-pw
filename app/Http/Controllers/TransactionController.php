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
            $query = $this->getTransactionListQuery(auth()->id());

            $this->filterTransactionList($query);

            $this->orderTransactionList($query);

            return $query->paginate(10);;
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

        $otherUser = \App\User::where('id', request('user_id'))
            ->where('name', request('user_name'))
            ->first();
            
        try{
            $transaction->createTransaction(
                auth()->user(),
                $otherUser,
                request('amount'));
        }
        catch(\Exception $e){
            return redirect()->back()
                ->with('error', 'Server error, please try again');
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Your transaction has been completed!');
    }

    public function getTransactionListQuery($userId)
    {
        $query =
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.transaction_type', 'tr.transaction_key')
            ->where('tr.user_id', $userId)
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.user_id', '!=', $userId)
            ->join('users as u', 'trans.user_id', '=', 'u.id')
        ;
        return $query;
    }

    public function filterTransactionList($query)
    {
        if (request('date')) {
            $query
                ->where('tr.created_at', 'like', '%' . request('date') . '%');
        }

        if (request('user_name')) {
            $query
                ->where('u.name', 'like', '%' . request('user_name') . '%');
        }

        if (request('amount')) {
            $query
                ->where('tr.amount',  'like', '%' . request('amount') . '%' );
        }

        if (request('user_balance')) {
            $query
                ->where('tr.user_balance', 'like', '%' . request('user_balance') . '%');
        }
    }

    public function orderTransactionList($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';

        if (request('sort') == 'date') {
            $query->orderBy('tr.created_at', $order);

        } elseif (request('sort') == 'user_name') {
            $query->orderBy('u.name', $order);
            
        } elseif (request('sort') == 'amount') {
            $query->orderBy('tr.amount', $order);

        } elseif (request('sort') == 'user_balance') {
            $query->orderBy('tr.user_balance', $order);

        } else {
            $query->orderBy('tr.id', 'desc');

        }
    }
}
