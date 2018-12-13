<?php

namespace App\Http\Controllers\Api;

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
                ->take(5)
                ->get();
        }
        
        return $users;
    }
}
