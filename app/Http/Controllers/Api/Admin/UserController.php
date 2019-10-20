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

        $page = (int)request()->get('limit');
        if(empty($page) || $page > 50){
            $page =  10;
        }

        return $this->formedSuccessResult($query->paginate($page));

    }

    public function show($id)
    {
        if (empty($user = User::find($id))) {
            return $this->formedErrorResult('Not found', 404);
        }

        return $this->formedSuccessResult($user);
    }

    public function update($id)
    {
        if (empty($user = User::find($id))) {
            return $this->formedErrorResult('Not found', 404);
        }

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
        foreach ($fields as $field) {
            if (!empty($value = request($field))) {
                $query
                    ->where($field, 'like', '%' . $value . '%');
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
