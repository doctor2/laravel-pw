<?php

namespace App\Http\Controllers;

use App\Rules\MaxUserBalance;
use App\Services\TransactionService;
use App\User;

class TransactionController extends BaseController
{
    private $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        if (request()->expectsJson()) {

            $query = $this->service->getTransactionsQueryWithFilter(auth()->id());

            return $this->formSuccessResult(datatables()->query($query)->make(true));
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

        if ($id = request('key')) {
            $oldTransaction = $this->service->getByIdAndUserId($id, $userId = auth()->id());

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
