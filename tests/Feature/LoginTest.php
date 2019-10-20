<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
        ])
            ->assertHeader('authorization')
        ;
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create([
            'password' => bcrypt('i-love-laravel'),
        ]);

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

       $response
           ->assertStatus(401)
           ->assertJson([
               'error' => 'login_error',
           ])
           ->assertHeaderMissing('authorization')
       ;
    }

}
