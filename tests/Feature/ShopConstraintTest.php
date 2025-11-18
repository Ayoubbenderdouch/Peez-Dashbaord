<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Neighborhood;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopConstraintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test: It enforces ONE shop per category per neighborhood
     */
    public function test_it_enforces_single_shop_per_neighborhood_category(): void
    {
        $neighborhood = Neighborhood::first();
        $category = Category::first();

        // Create first shop - should succeed
        $shop1 = Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'First Shop',
            'discount_percent' => 6.50,
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop1->id,
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
        ]);

        // Try to create second shop with same neighborhood + category - should fail
        $this->expectException(\Illuminate\Database\QueryException::class);

        Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'Second Shop - Should Fail',
            'discount_percent' => 7.00,
            'lat' => 35.6970000,
            'lng' => -0.6332000,
            'phone' => '+213551234568',
            'is_active' => true,
        ]);
    }

    /**
     * Test: Shop can be created with same category in different neighborhood
     */
    public function test_shop_can_be_created_with_same_category_in_different_neighborhood(): void
    {
        $neighborhoods = Neighborhood::take(2)->get();
        $category = Category::first();

        // Create shop in first neighborhood
        $shop1 = Shop::create([
            'neighborhood_id' => $neighborhoods[0]->id,
            'category_id' => $category->id,
            'name' => 'Shop in Neighborhood 1',
            'discount_percent' => 6.50,
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);

        // Create shop with same category in second neighborhood - should succeed
        $shop2 = Shop::create([
            'neighborhood_id' => $neighborhoods[1]->id,
            'category_id' => $category->id,
            'name' => 'Shop in Neighborhood 2',
            'discount_percent' => 7.00,
            'lat' => 35.6970000,
            'lng' => -0.6332000,
            'phone' => '+213551234568',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shops', ['id' => $shop1->id]);
        $this->assertDatabaseHas('shops', ['id' => $shop2->id]);
    }

    /**
     * Test: Shop can be created with different category in same neighborhood
     */
    public function test_shop_can_be_created_with_different_category_in_same_neighborhood(): void
    {
        $neighborhood = Neighborhood::first();
        $categories = Category::take(2)->get();

        // Create shop with first category
        $shop1 = Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $categories[0]->id,
            'name' => 'Shop Category 1',
            'discount_percent' => 6.50,
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234567',
            'is_active' => true,
        ]);

        // Create shop with second category - should succeed
        $shop2 = Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $categories[1]->id,
            'name' => 'Shop Category 2',
            'discount_percent' => 7.00,
            'lat' => 35.6970000,
            'lng' => -0.6332000,
            'phone' => '+213551234568',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shops', ['id' => $shop1->id]);
        $this->assertDatabaseHas('shops', ['id' => $shop2->id]);
    }
}
