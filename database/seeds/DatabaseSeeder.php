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
        factory(App\User::class, 5)->create();

        $users = App\User::all();

        $myUser = factory(App\User::class)->create([
            'name' => 'brath1',
            'email' => 'brath1@example.org',
            'password' => '$2y$10$SHOpPjq2SAlrZ.Uv1ljZjuYY1eSt2Df57E8tBTYh8fby65tCflnX.',
        ]);

        foreach ($users as $user) {
            $amount = rand(100, 400);
           
            $this->createTransaction($user, $myUser, $amount);
        }
        foreach ($users as $user) {
            $amount = rand(100, 300);
           
            $this->createTransaction($myUser, $user, $amount);
        }

    }

    public function createTransaction($debit_user, $credit_user, $amount)
    {
        $transaction_key = str_random(32);
        
        $debit_user_balance = $debit_user->balance->fresh()->balance - $amount;
        $credit_user_balance = $credit_user->balance->fresh()->balance + $amount;

        $tr1 = \App\Transaction::create([
            'user_id' => $credit_user->id,
            'transaction_key' => $transaction_key,
            'amount' => $amount,
            'user_balance' => $credit_user_balance,
            'transaction_type' => 'CREDIT',
        ]);

        $tr2 = \App\Transaction::create([
            'user_id' => $debit_user->id,
            'transaction_key' => $transaction_key,
            'amount' => $amount,
            'user_balance' => $debit_user_balance,
            'transaction_type' => 'DEBIT',
        ]);
        
        if ($tr1 && $tr2) {
            $debit_user->balance()->update(['balance' => $debit_user_balance]);
            $credit_user->balance()->update(['balance' => $credit_user_balance]);
        }
    }
}
