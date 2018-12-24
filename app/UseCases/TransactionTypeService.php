<?php

namespace App\UseCases;

use App\TransactionType;


class TransactionTypeService
{
    public function getDebitId()
    {
        return TransactionType::where('name', TransactionType::DEBIT)->pluck('id')->first();
    }

    public function getCreditId()
    {
        return TransactionType::where('name', TransactionType::CREDIT)->pluck('id')->first();
    }
}
