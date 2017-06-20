<?php

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TwoFactorAuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    function test_authentication_can_be_enabled()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                ->json('POST', '/settings/two-factor-auth', [
                    'country_code' => '1',
                    'phone' => '4792266733',
                ]);

        $response->assertStatus(200);

        $user = $user->fresh();

        $this->assertTrue(! is_null($user->authy_id));
    }


    function test_country_code_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                ->json('POST', '/settings/two-factor-auth', [
                    'country_code' => '',
                    'phone' => '4792266733',
                ]);

        $response->assertStatus(422);
    }


    public function test_phone_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                ->json('POST', '/settings/two-factor-auth', [
                    'country_code' => '1',
                    'phone' => '',
                ]);

        $response->assertStatus(422);
    }


    public function test_authentication_can_be_disabled()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                ->json('POST', '/settings/two-factor-auth', [
                    'country_code' => '1',
                    'phone' => '4792266733',
                ]);

        $response->assertStatus(200);

        $user = $user->fresh();
        
        $this->assertTrue(! is_null($user->authy_id));

        $this->actingAs($user)
            ->json('DELETE', '/settings/two-factor-auth', [])
            ->assertStatus(200);;

        $user = $user->fresh();

        $this->assertTrue(is_null($user->authy_id));
    }
}
