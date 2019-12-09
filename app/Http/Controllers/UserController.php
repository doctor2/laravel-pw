<?php

namespace App\Http\Controllers;

class UserController extends BaseController
{
    public function index()
    {
        $users = [];
        if ($name = trim(request('name'))) {
            $users = [];
            \App\User::where('name', 'like', '%' . $name . '%')
                ->select(['name', 'id'])
                ->where('id', '!=', auth()->id())
                ->take(10)
                ->get()
                ->map(function ($user) use (&$users) {
                    $users[] = [
                        'name' => $user->name,
                        'id' => $user->id
                    ];
                });
        }

        return $this->formSuccessResult($users);
    }
}
