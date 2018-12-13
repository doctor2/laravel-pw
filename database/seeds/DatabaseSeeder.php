<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 25)->create();

        $users = App\User::all();

        $myUser = factory(App\User::class)->create([
            'name' => 'brath1',
            'email' => 'brath1@example.org',
            'password' => '$2y$10$SHOpPjq2SAlrZ.Uv1ljZjuYY1eSt2Df57E8tBTYh8fby65tCflnX.',
        ]);
        $transaction = new \App\Transaction();

        foreach ($users as $user) {
            $amount = rand(100, 400);
           
            $transaction->createTransaction($user, $myUser, $amount);
        }
        foreach ($users as $user) {
            $amount = rand(100, 300);
           
            $transaction->createTransaction($myUser, $user, $amount);
        }

    }
}
