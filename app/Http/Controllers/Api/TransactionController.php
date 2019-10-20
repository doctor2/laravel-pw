<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Rules\MaxUserBalance;
use App\Services\TransactionService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class TransactionController extends BaseController
{
    private $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $query = $this->service->getTransactionsQueryWithFilterAndOrder(auth()->id());

        $page = (int)request()->get('limit');
        if(empty($page) || $page > 50){
            $page =  10;
        }

        return $this->formedSuccessResult($query->paginate($page));
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

        return $this->formedSuccessResult($transaction);
    }

    public function store(MaxUserBalance $maxBalance)
    {
        $rules = [
            'amount' => ['required', 'numeric', 'min:1', $maxBalance],
            'user_name' => 'required|exists:users,name',
        ];
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->formedErrorResult('The given data was invalid.', 422,  $validator->errors());
        }

        $otherUser = User::where('id', request('user_id'))
            ->where('name', request('user_name'))
            ->first();

        try {
            $this->service->create(auth()->user(), $otherUser, request('amount'));
        } catch (\Exception $e) {
            return $this->formedErrorResult('Server error, please try again');
        }

        return $this->formedSuccessResult(['balance' => Auth::user()->fresh()->currentBalance]);
    }
}
