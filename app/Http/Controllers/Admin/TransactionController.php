<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTransactionService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private $adminService;

    public function __construct(?AdminTransactionService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        if (request()->expectsJson()) {

            $query = $this->adminService->getTransactionListQuery();

            $this->adminService->filterTransactionList($query);

            $this->adminService->orderTransactionList($query);

            return $query->paginate(10);
        }

        return view('admin.transactions.index');
    }

    public function show($key)
    {
        $transaction = $this->adminService->getByKey($key);

        if (empty($transaction)) {
            abort(404);
        }

        return view('admin.transactions.show', compact('transaction'));
    }

    public function update($key)
    {
        request()->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $this->adminService->update($key, request('amount'));

        return json_encode($this->adminService->getByKey($key));
    }

}
