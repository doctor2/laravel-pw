<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AdminTransactionService;
use Validator;

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

        return $this->formSuccessResult($query->paginate($page));
    }

    public function edit($id)
    {
        $transaction = $this->adminService->getById($id);

        if (empty($transaction)) {
            $this->formErrorResult('Not found', 404);
        }

        return $this->formSuccessResult($transaction);
    }

    public function update($id)
    {
        $rules = [
            'amount' => ['required', 'numeric', 'min:1'],
        ];
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->formErrorResult('The given data was invalid.', 422,  $validator->errors());
        }

        try {
            $this->adminService->update($id, request('amount'));
        } catch (\Exception $e) {
            return $this->formErrorResult($e->getMessage(), 400);
        }

        return $this->formSuccessResult($this->adminService->getById($id));
    }
}
