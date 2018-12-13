<?php

namespace App\Http\Controllers;

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
        if (request()->expectsJson())
        {
            $transactionsQuery = $this->getTransactionsListQuery(auth()->id());

            $this->filterTransactionList($transactionsQuery);

            $transactions =  $transactionsQuery// ->get();
            ->paginate(5);

            return $transactions;
        }

        return view('transactions.index');
    }

    public function create()
    {
        $transaction = [
            'user_name' => '',
            'id' => '',
            'amount' => ''
        ];

        if(request('key')){
            $oldTransaction = \App\Transaction::where('transaction_key', request('key'))
                ->where('user_id', '!=', auth()->id())
                ->with('user')
                ->first();
            if($oldTransaction){
                $transaction['user_name'] =  $oldTransaction->user->name;
                $transaction['id'] =  $oldTransaction->user->id;
                $transaction['amount'] =  $oldTransaction->amount;
            }
        }

        return view('transactions.create', compact('transaction'));
    }

    public function store()
    {
    }


    public function getTransactionsListQuery($userId)  
    {
        $transactionsQuery = 
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.transaction_type', 'tr.transaction_key' )
            ->where('tr.user_id', $userId)
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.user_id','!=', $userId)
            ->join('users as u', 'trans.user_id', '=', 'u.id')
            ->orderBy('tr.id', 'desc')
            ;
            return $transactionsQuery;
    }

    public function filterTransactionList($transactionsQuery)
    {
        if(request('date')){
            $transactionsQuery
                ->where('tr.created_at', 'like', '%'.  request('date') . '%');
        }

        if(request('user_name')){
            $transactionsQuery
                ->where('u.name', 'like', '%'. request('user_name') . '%');
        }

        if(request('amount')){
            $transactionsQuery
                ->where('tr.amount', request('amount'));
        }

        if(request('user_balance')){
            $transactionsQuery
                ->where('tr.user_balance', request('user_balance'));
        }

        return $transactionsQuery;
    }
}
