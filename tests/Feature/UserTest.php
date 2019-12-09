<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_edit_users()
    {
        $this->withExceptionHandling();

        $this->loginAdmin();

        $user = factory(User::class)->create();
        $user->name .= ' new';
        $user->banned = true;
        $this->patch('/api/admin/users/' . $user->id, $data = [
            'name' => $user->name,
            'email' => $user->email,
            'banned' => $user->banned
        ])->assertStatus(200)
            ->assertSee('success');

        $userData = User::find($user->id)->toArray();
        $userData = array_intersect_key($userData, ['name' => '', 'email' => '', 'banned' => '']);
        $this->assertEquals($data, $userData);
    }

    public function test_user_cant_get_admins_pages()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create();
        $this->actingAs($user);
        $responses = [
            $this->get('/api/admin/users'),
            $this->get('/api/admin'),
            $this->patch('/api/admin/users/1'),
            $this->patch('/api/admin/transactions/1')
        ];
        foreach ($responses as $response) {
            $response
                ->assertStatus(403)
                ->assertSee('You do not have a permission to lock this thread');
        }
    }
}
