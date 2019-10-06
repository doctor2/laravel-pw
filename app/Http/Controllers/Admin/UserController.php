<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        if (request()->expectsJson()) {
            $fields = ['name', 'email', 'banned', 'created_at'];

            $query = \DB::table('users')->select(array_merge($fields, ['id']));

            $this->filterUsers($query, $fields);

            return $this->formedSuccessResult(datatables()->query($query)->make(true));
        }

        return view('admin.users.index');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(User $user)
    {
        $user->update(request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'banned' => 'required',
        ])
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User has been updated!');
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
