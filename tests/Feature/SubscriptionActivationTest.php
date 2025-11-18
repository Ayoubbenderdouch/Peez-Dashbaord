<?php

namespace Tests\Feature;

use App\Models\Activation;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test: It activates subscription for 1 month correctly
     */
    public function test_it_activates_subscription_for_1_month(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();

        // Create activation for 1 month
        $activation = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 1,
        ]);

        // Create subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'start_at' => now(),
            'end_at' => now()->addMonths(1),
            'status' => 'active',
            'source' => 'vendor',
        ]);

        $this->assertEquals(1, $activation->months);
        $this->assertEquals(300, $activation->amount_dzd);
        $this->assertTrue($subscription->isActive());
        $this->assertEquals($user->id, $subscription->user_id);
    }

    /**
     * Test: It activates subscription for 2 months correctly
     */
    public function test_it_activates_subscription_for_2_months(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();

        $activation = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 2,
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'start_at' => now(),
            'end_at' => now()->addMonths(2),
            'status' => 'active',
            'source' => 'vendor',
        ]);

        $this->assertEquals(2, $activation->months);
        $this->assertEquals(600, $activation->amount_dzd);
    }

    /**
     * Test: It activates subscription for 3 months correctly
     */
    public function test_it_activates_subscription_for_3_months(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();

        $activation = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 3,
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'start_at' => now(),
            'end_at' => now()->addMonths(3),
            'status' => 'active',
            'source' => 'vendor',
        ]);

        $this->assertEquals(3, $activation->months);
        $this->assertEquals(900, $activation->amount_dzd);
    }

    /**
     * Test: It extends existing active subscription
     */
    public function test_it_extends_existing_active_subscription(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();

        // Create initial subscription for 1 month
        $originalEndDate = now()->addMonths(1);
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'start_at' => now(),
            'end_at' => $originalEndDate,
            'status' => 'active',
            'source' => 'vendor',
        ]);

        // Extend by 2 more months
        $subscription->end_at = $subscription->end_at->addMonths(2);
        $subscription->save();

        Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 2,
        ]);

        $subscription->refresh();
        $this->assertEquals(
            $originalEndDate->copy()->addMonths(2)->format('Y-m-d'),
            $subscription->end_at->format('Y-m-d')
        );
        $this->assertTrue($subscription->isActive());
    }

    /**
     * Test: Amount is auto-calculated as months * 300
     */
    public function test_amount_is_auto_calculated_correctly(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();

        // Test 1 month
        $activation1 = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 1,
        ]);
        $this->assertEquals(300, $activation1->amount_dzd);

        // Test 2 months
        $activation2 = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 2,
        ]);
        $this->assertEquals(600, $activation2->amount_dzd);

        // Test 3 months
        $activation3 = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => 3,
        ]);
        $this->assertEquals(900, $activation3->amount_dzd);
    }
}
