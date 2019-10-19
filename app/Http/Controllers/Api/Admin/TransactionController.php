<?php

namespace App\Http\Controllers\Api\Admin;

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
        $query = $this->adminService->getTransactionListQuery();

        $this->adminService->filterTransactionList($query);

        $this->adminService->orderTransactionList($query);

        $page = (int)request()->get('limit');
        if(empty($page) || $page > 50){
            $page =  10;
        }

        return $this->formedSuccessResult($query->paginate($page));
    }

    public function edit($id)
    {
        $transaction = $this->adminService->getById($id);

        if (empty($transaction)) {
            $this->formedErrorResult('Not found', 404);
        }

        return $this->formedSuccessResult($transaction);
    }

    public function update($id)
    {
        request()->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            $this->adminService->update($id, request('amount'));
        } catch (\Exception $e) {
            return $this->formedErrorResult($e->getMessage(), 400);
        }

        return $this->formedSuccessResult($this->adminService->getById($id));
    }
}
