<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;

    protected $user2;

    protected function setUp()
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();

        $this->actingAs($this->user1);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_user_can_create_transaction_to_another_user()
    {
        $balance1 = $this->user1->balance->balance;
        $balance2 = $this->user2->balance->balance;

        $this->post(route('transactions.store'),$data = [
            'amount' => 333,
            'user_id' => $this->user2->id,
            'user_name' => $this->user2->name
        ])
            ->assertStatus(302)
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success','Your transaction has been completed!')
        ;

        $this->assertEquals($this->user1->fresh()->balance->balance, $balance1 - $data['amount']);
        $this->assertEquals($this->user2->fresh()->balance->balance, $balance2 + $data['amount']);
    }

    public function test_an_error_while_user_create_transaction()
    {
        $this->post(route('transactions.store'),$data = [
            'amount' => 333,
            'user_name' => $this->user2->name
        ])
            ->assertStatus(302)
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('error','Server error, please try again')
        ;

        $this->post(route('transactions.store'),$data = [
        ])
            ->assertStatus(302)
            ->assertSessionHasErrors('user_name')
            ->assertSessionHasErrors('amount')
        ;
    }

    public function test_an_error_while_user_try_to_transact_more_pw_than_he_can()
    {
        $balance1 = $this->user1->balance->balance;
        $this->post(route('transactions.store'),$data = [
            'amount' => (int)$balance1 + 150,
            'user_id' => $this->user2->id,
            'user_name' => $this->user2->name
        ])
            ->assertStatus(302)
            ->assertSessionHasErrors('amount')
        ;

    }

    public function test_an_error_while_user_try_to_transact_pw_less_than_zero()
    {
        $this->post(route('transactions.store'),$data = [
            'amount' => -50,
            'user_id' => $this->user2->id,
            'user_name' => $this->user2->name
        ])
            ->assertStatus(302)
            ->assertSessionHasErrors('amount')
        ;

    }
}
