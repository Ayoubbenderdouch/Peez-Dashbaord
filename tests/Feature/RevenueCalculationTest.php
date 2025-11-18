<?php

namespace Tests\Feature;

use App\Models\Activation;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevenueCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test: It calculates monthly revenue as activations count × 300 DZD
     */
    public function test_it_calculates_monthly_revenue_as_activations_times_300(): void
    {
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();
        $users = User::factory()->count(5)->create();

        // Create activations for this month
        $activationsThisMonth = 0;
        foreach ($users as $user) {
            Activation::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'vendor_id' => $vendor->id,
                'months' => rand(1, 3),
                'created_at' => now(),
            ]);
            $activationsThisMonth++;
        }

        // Calculate expected revenue
        $expectedRevenue = $activationsThisMonth * 300;

        // Get actual activations
        $actualActivations = Activation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $actualRevenue = $actualActivations * 300;

        $this->assertEquals($expectedRevenue, $actualRevenue);
        $this->assertEquals(5 * 300, $actualRevenue); // 5 activations × 300 DZD
        $this->assertEquals(1500, $actualRevenue);
    }

    /**
     * Test: Revenue calculation per shop
     */
    public function test_revenue_calculation_per_shop(): void
    {
        $shops = Shop::take(3)->get();
        $vendor = User::where('is_vendor', true)->first();

        // Create different number of activations per shop
        foreach ($shops as $index => $shop) {
            $activationsCount = ($index + 1) * 2; // 2, 4, 6 activations

            for ($i = 0; $i < $activationsCount; $i++) {
                $user = User::factory()->create();
                Activation::create([
                    'user_id' => $user->id,
                    'shop_id' => $shop->id,
                    'vendor_id' => $vendor->id,
                    'months' => 1,
                    'created_at' => now(),
                ]);
            }

            // Calculate revenue for this shop
            $shopActivations = Activation::where('shop_id', $shop->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $shopRevenue = $shopActivations * 300;

            $this->assertEquals($activationsCount * 300, $shopRevenue);
        }
    }

    /**
     * Test: Total revenue calculation for all activations
     */
    public function test_total_revenue_calculation(): void
    {
        $vendor = User::where('is_vendor', true)->first();
        $shop = Shop::first();

        // Create 10 activations with different months
        $totalActivations = 0;
        $expectedTotal = 0;

        for ($i = 1; $i <= 10; $i++) {
            $user = User::factory()->create();
            $months = ($i % 3) + 1; // Alternates between 1, 2, 3

            Activation::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'vendor_id' => $vendor->id,
                'months' => $months,
                'created_at' => now(),
            ]);

            $totalActivations++;
            $expectedTotal += $months * 300;
        }

        // Calculate actual total
        $actualTotal = Activation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_dzd');

        $this->assertEquals($expectedTotal, $actualTotal);
        $this->assertGreaterThan(0, $actualTotal);
    }

    /**
     * Test: Monthly revenue is zero when no activations
     */
    public function test_monthly_revenue_is_zero_when_no_activations(): void
    {
        // Clear all activations from current month
        Activation::whereMonth('created_at', now()->month)->delete();

        $revenue = Activation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count() * 300;

        $this->assertEquals(0, $revenue);
    }

    /**
     * Test: Revenue calculation respects month filter
     */
    public function test_revenue_calculation_respects_month_filter(): void
    {
        $shop = Shop::first();
        $vendor = User::where('is_vendor', true)->first();

        // Create activations for current month
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->create();
            Activation::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'vendor_id' => $vendor->id,
                'months' => 1,
                'created_at' => now(),
            ]);
        }

        // Create activations for last month
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            Activation::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'vendor_id' => $vendor->id,
                'months' => 1,
                'created_at' => now()->subMonth(),
            ]);
        }

        $currentMonthRevenue = Activation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count() * 300;

        $lastMonthRevenue = Activation::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count() * 300;

        $this->assertEquals(900, $currentMonthRevenue); // 3 × 300
        $this->assertEquals(1500, $lastMonthRevenue); // 5 × 300
    }
}
