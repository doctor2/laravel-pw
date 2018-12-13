<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $fields = ['name', 'email', 'banned', 'created_at'];

        if (request()->expectsJson()) {
            $query = User::select( array_merge($fields, ['id']) );

            $this->filterUsers($query, $fields);

            $this->orderUsers($query, $fields);

            return $query->paginate(5);
        }

        return view('admin.users.index');
    }

    public function show(User $user)
    {
        return  view('admin.users.show', compact('user'));
    }

    public function filterUsers($query, $fields)
    {
        foreach ($fields as $field) {
            if (request($field)) {
                $query
                    ->where($field, 'like', '%' . request($field) . '%');
            }
        }
    }

    public function orderUsers($query, $fields)
    {
        $order = request('order') == 'asc' ? 'asc' : 'desc';

        foreach ($fields as $field) {
            if (request('sort') == $field) {
                $query
                    ->orderBy($field, $order);
            }
        }
    }
}
