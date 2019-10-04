<?php

use App\Services\TransactionService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(TransactionService $service)
    {
        factory(App\User::class, 25)->create();

        $users = App\User::all();

        $myUser = factory(App\User::class)->create([
            'name' => 'brath1',
            'email' => 'brath1@example.org',
            'password' => bcrypt(123123123),
        ]);

        foreach ($users as $user) {
            $amount = rand(100, 400);

            $service->create($user, $myUser, $amount);
        }

        foreach ($users as $user) {
            $amount = rand(100, 300);

            $service->create($myUser, $user, $amount);
        }
    }
}
