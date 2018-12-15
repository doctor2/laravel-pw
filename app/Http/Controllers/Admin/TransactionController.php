<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function index()
    {
        if (request()->expectsJson()) {
            $query = $this->getTransactionListQuery();

            $this->filterTransactionList($query);

            $this->orderTransactionList($query);

            $transactions = $query // ->get();
            ->paginate(10);

            return $transactions;
        }

        return view('admin.transactions.index');
    }

    public function show($key, TransactionService $service)
    {
        $transaction = $service->getByKey($key);

        if (empty($transaction)) {
            abort(404);
        }

        return view('admin.transactions.show', compact('transaction'));
    }

    public function update($key, TransactionService $service)
    {
        request()->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $service->update($key, request('amount'));

        return json_encode($service->getByKey($key));
    }

    public function getTransactionListQuery()
    {
        $query =
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.created_at', 'u.name as debit_user_name', 'user.name as crebit_user_name', 'tr.transaction_key')
            ->where('tr.transaction_type', config('transaction.types.debit'))
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.transaction_type', config('transaction.types.credit'))
            ->join('users as user', 'trans.user_id', '=', 'user.id')
            ->join('users as u', 'tr.user_id', '=', 'u.id')
        ;
        return $query;
    }

    public function filterTransactionList($query)
    {
        if (request('date')) {
            $query
                ->where('tr.created_at', 'like', '%' . request('date') . '%');
        }

        if (request('debit_user_name')) {
            $query
                ->where('u.name', 'like', '%' . request('debit_user_name') . '%');
        }

        if (request('crebit_user_name')) {
            $query
                ->where('user.name', 'like', '%' . request('crebit_user_name') . '%');
        }

        if (request('amount')) {
            $query
                ->where('tr.amount', 'like', '%' . request('amount') . '%');
        }

    }

    public function orderTransactionList($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';

        if (request('sort') == 'date') {
            $query->orderBy('tr.created_at', $order);

        } elseif (request('sort') == 'debit_user_name') {
            $query->orderBy('u.name', $order);

        } elseif (request('sort') == 'amount') {
            $query->orderBy('tr.amount', $order);

        } elseif (request('sort') == 'crebit_user_name') {
            $query->orderBy('user.name', $order);

        } else {
            $query->orderBy('tr.id', 'desc');

        }
    }
}
