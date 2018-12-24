<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\TransactionService;
use App\UseCases\TransactionTypeService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function index()
    {
        if (request()->expectsJson()) {
            $query = $this->getTransactionListQuery();

            $this->filterTransactionList($query);

            $this->orderTransactionList($query);

            return $query->paginate(10);
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
        $typeService = new TransactionTypeService();
        $query =
        \DB::table('transactions as tr')
            ->select('tr.amount', 'tr.created_at', 'u.name as debit_user_name', 'user.name as crebit_user_name', 'tr.transaction_key')
            ->where('tr.transaction_type_id', $typeService->getDebitId())
            ->join('transactions as trans', 'tr.transaction_key', '=', 'trans.transaction_key')
            ->where('trans.transaction_type_id', $typeService->getCreditId())
            ->join('users as user', 'trans.user_id', '=', 'user.id')
            ->join('users as u', 'tr.user_id', '=', 'u.id')
        ;
        return $query;
    }

    public function filterTransactionList($query)
    {
        if (!empty($value = request('date'))) {
            $query
                ->where('tr.created_at', 'like', '%' . request('date') . '%');
        }

        if (!empty($value = request('debit_user_name'))) {
            $query
                ->where('u.name', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('crebit_user_name'))) {
            $query
                ->where('user.name', 'like', '%' . $value . '%');
        }

        if (!empty($value = request('amount'))) {
            $query
                ->where('tr.amount', 'like', '%' . $value . '%');
        }

    }

    public function orderTransactionList($query)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';
        $sort = request('sort');

        if ($sort == 'date') {
            $query->orderBy('tr.created_at', $order);

        } elseif ($sort == 'debit_user_name') {
            $query->orderBy('u.name', $order);

        } elseif ($sort == 'amount') {
            $query->orderBy('tr.amount', $order);

        } elseif ($sort == 'crebit_user_name') {
            $query->orderBy('user.name', $order);

        } else {
            $query->orderBy('tr.id', 'desc');

        }
    }
}
