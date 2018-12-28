<?php

namespace App\Http\Controllers;

use App\Rules\MaxUserBalance;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use App\TransactionAmount;
use App\User;

class TransactionController extends Controller
{
    private $service;

    public function __construct(TransactionService $service)
    {
        $this->middleware('auth');

        $this->service = $service;
    }

    public function index()
    {
        if (request()->expectsJson()) {

            $query = $this->service->getTransactionsQueryWithFilterAndOrder(auth()->id());

            return $query->paginate(10);
        }

        return view('transactions.index');
    }

    public function create()
    {
        $transaction = $this->service->getByKeyForCurentUser(request('key'));

        return view('transactions.create', compact('transaction'));
    }

    public function store(MaxUserBalance $maxBalance)
    {
        request()->validate([
            'amount' => ['required', 'numeric', 'min:1', $maxBalance],
            'user_name' => 'required|exists:users,name',
        ]);

        $otherUser = User::where('id', request('user_id'))
            ->where('name', request('user_name'))
            ->first();

        try {
            $this->service->create(auth()->user(), $otherUser, request('amount'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Server error, please try again');
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Your transaction has been completed!');
    }
}
