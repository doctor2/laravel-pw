<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\User;
use Validator;

class UserController extends BaseController
{
    public function index()
    {
        $fields = ['name', 'email', 'banned', 'created_at'];

        $query = \DB::table('users')->select(array_merge($fields, ['id']));

        $this->filterUsers($query, $fields);

        $this->orderUsers($query, $fields);

        $page = (int)request()->get('limit');
        if (empty($page) || $page > 50) {
            $page = 10;
        }

        return $this->formSuccessResult($query->paginate($page));

    }

    public function show($id)
    {
        if (empty($user = User::find($id))) {
            return $this->formErrorResult('Not found', 404);
        }

        return $this->formSuccessResult($user);
    }

    public function update($id)
    {
        if (empty($user = User::find($id))) {
            return $this->formErrorResult('Not found', 404);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'banned' => 'required',
        ];
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->formErrorResult('update_validation_error', 422,  $validator->errors());
        }

        $user->update(request()->all());

        return $this->formSuccessResult($user);
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
