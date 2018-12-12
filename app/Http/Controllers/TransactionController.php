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
        $user = auth()->user();

        $transactions = 
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.user_balance', 'tr.created_at', 'u.name as user_name', 'tr.transaction_type' )
            ->where('tr.user_id', $user->id)
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.user_id','!=', $user->id)
            ->join('users as u', 'trans.user_id', '=', 'u.id')
            ->orderBy('tr.id', 'desc')
            ->get();
            // ->paginate(5);

        if (request()->expectsJson())
        {
            return $transactions;
        }

        return view('transactions.index', compact('transactions'));
    }

}
