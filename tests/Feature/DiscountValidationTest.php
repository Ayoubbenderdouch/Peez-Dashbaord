<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Neighborhood;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DiscountValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test: It rejects discount below 5%
     */
    public function test_it_rejects_discount_out_of_range_below_minimum(): void
    {
        $neighborhood = Neighborhood::first();
        $category = Category::first();

        $this->expectException(\PDOException::class);

        Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'Invalid Shop - Low Discount',
            'discount_percent' => 4.99, // Below minimum
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);
    }

    /**
     * Test: It rejects discount above 8%
     */
    public function test_it_rejects_discount_out_of_range_above_maximum(): void
    {
        $neighborhood = Neighborhood::first();
        $category = Category::first();

        $this->expectException(\PDOException::class);

        Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'Invalid Shop - High Discount',
            'discount_percent' => 8.01, // Above maximum
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);
    }

    /**
     * Test: It accepts discount exactly at 5%
     */
    public function test_it_accepts_discount_at_minimum_5_percent(): void
    {
        $neighborhood = Neighborhood::first();
        $category = Category::first();

        $shop = Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'Valid Shop - 5% Discount',
            'discount_percent' => 5.00,
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'discount_percent' => 5.00,
        ]);
    }

    /**
     * Test: It accepts discount exactly at 8%
     */
    public function test_it_accepts_discount_at_maximum_8_percent(): void
    {
        $neighborhood = Neighborhood::first();
        $category = Category::first();

        $shop = Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'Valid Shop - 8% Discount',
            'discount_percent' => 8.00,
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'discount_percent' => 8.00,
        ]);
    }

    /**
     * Test: It accepts discount in valid range
     */
    public function test_it_accepts_discount_in_valid_range(): void
    {
        $neighborhood = Neighborhood::first();
        $categories = Category::take(3)->get();

        // Test various valid discounts
        $validDiscounts = [5.50, 6.75, 7.25];

        foreach ($validDiscounts as $index => $discount) {
            $shop = Shop::create([
                'neighborhood_id' => $neighborhood->id,
                'category_id' => $categories[$index]->id,
                'name' => "Valid Shop - {$discount}% Discount",
                'discount_percent' => $discount,
                'lat' => 35.6969744,
                'lng' => -0.6331195,
                'phone' => '+21355123456' . $index,
                'is_active' => true,
            ]);

            $this->assertDatabaseHas('shops', [
                'id' => $shop->id,
                'discount_percent' => $discount,
            ]);
        }
    }
}
