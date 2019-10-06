<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = [];
        if(request('name')){
            $users = \App\User::where('name', 'like', '%'. request('name') . '%')
                ->where('id', '!=', auth()->id())
                ->take(10)
                ->get();
        }

        return $users;
    }
}
