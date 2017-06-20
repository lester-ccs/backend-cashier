<?php

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @group braintree
 */
class SubscribeBraintreeTest extends TestCase
{
    use DatabaseMigrations;  //InteractsWithPaymentProviders

    public function test_users_can_subscribe()
    {
        $response = $this->actingAs($user = factory(User::class)->create())
                ->json('POST', '/settings/subscription', [
                    'braintree_token' => 'fake-valid-nonce',
                    'braintree_type' => 'credit-card',
                    'plan' => 'basic-plan',
                ]);

        $user = $user->fresh();

        $response->assertStatus(200);

        $this->assertTrue($user->subscribed());

        $this->assertEquals('basic-plan', $user->subscription()->braintree_plan);
    }


    public function test_braintree_token_is_required()
    {
        $this->actingAs($user = factory(User::class)->create())
                ->json('POST', '/settings/subscription', [
                    'braintree_token' => '',
                    'plan' => 'spark-test-1',
                ])->assertStatus(422);
    }


    public function test_braintree_type_is_required()
    {
        $this->actingAs($user = factory(User::class)->create())
                ->json('POST', '/settings/subscription', [
                    'braintree_token' => 'fake-valid-nonce',
                    'braintree_type' => '',
                    'plan' => 'spark-test-1',
                ])->assertStatus(422);
    }


    public function test_plan_name_must_be_a_valid_plan()
    {
        $this->actingAs($user = factory(User::class)->create())
                ->json('POST', '/settings/subscription', [
                    'braintree_token' => 'fake-valid-nonce',
                    'plan' => 'spark-test-10',
                ])->assertStatus(422);
    }
}
