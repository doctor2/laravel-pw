<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AdminTransactionService;

class TransactionController extends BaseController
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

            return $this->formSuccessResult(datatables()->query($query)->make(true));
        }

        return view('admin.transactions.index');
    }

    public function show($id)
    {
        $transaction = $this->adminService->getById($id);

        if (empty($transaction)) {
            abort(404);
        }

        return view('admin.transactions.show', compact('transaction'));
    }

    public function update($id)
    {
        request()->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            $this->adminService->update($id, (int)request('amount'));
        } catch (\Exception $e) {
            return $this->formErrorResult($e->getMessage(), 400);
        }

        return $this->formSuccessResult($this->adminService->getById($id));
    }
}
