<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTransactionService;
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
            $this->adminService->update($id, request('amount'));
        } catch (\Exception $e) {
            return response([
                'code' => '400',
                'error' => $e->getMessage(),
            ], 400);
        }

        return json_encode($this->adminService->getById($id));
    }

}
