<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\User;

class UserController extends BaseController
{
    public function index()
    {
        $fields = ['name', 'email', 'banned', 'created_at'];

        $query = \DB::table('users')->select(array_merge($fields, ['id']));

        $this->filterUsers($query, $fields);

        $this->orderUsers($query, $fields);

        return $this->formedSuccessResult($query->paginate(10));

    }

    public function show(User $user)
    {
        return $this->formedSuccessResult($user);
    }

    public function update(User $user)
    {
        $user->update(request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'banned' => 'required',
        ])
        );

        return $this->formedSuccessResult($user);
    }

    public function filterUsers($query, $fields)
    {
        $q = request('search');
        if (empty($q['value']) || empty($q = trim($q['value']))) {
            return;
        }

        foreach ($fields as $field) {
            $query->orWhere($field, 'like', '%' . $q . '%');
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
