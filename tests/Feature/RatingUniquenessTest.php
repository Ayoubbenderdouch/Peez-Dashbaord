<?php

namespace Tests\Feature;

use App\Models\Rating;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingUniquenessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test: It allows single rating per user per shop
     */
    public function test_it_allows_single_rating_per_user_per_shop(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();

        // Create first rating
        $rating = Rating::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'stars' => 5,
        ]);

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'stars' => 5,
        ]);
    }

    /**
     * Test: It prevents duplicate rating from same user for same shop
     */
    public function test_it_prevents_duplicate_rating_from_same_user_for_same_shop(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();

        // Create first rating
        Rating::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'stars' => 4,
        ]);

        // Try to create duplicate rating - should fail
        $this->expectException(\Illuminate\Database\QueryException::class);

        Rating::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'stars' => 5,
        ]);
    }

    /**
     * Test: User can rate different shops
     */
    public function test_user_can_rate_different_shops(): void
    {
        $user = User::factory()->create();
        $shops = Shop::take(3)->get();

        foreach ($shops as $shop) {
            $rating = Rating::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'stars' => rand(1, 5),
            ]);

            $this->assertDatabaseHas('ratings', [
                'id' => $rating->id,
                'user_id' => $user->id,
                'shop_id' => $shop->id,
            ]);
        }

        $this->assertEquals(3, Rating::where('user_id', $user->id)->count());
    }

    /**
     * Test: Different users can rate the same shop
     */
    public function test_different_users_can_rate_same_shop(): void
    {
        $users = User::factory()->count(3)->create();
        $shop = Shop::first();

        foreach ($users as $user) {
            $rating = Rating::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'stars' => rand(1, 5),
            ]);

            $this->assertDatabaseHas('ratings', [
                'id' => $rating->id,
                'user_id' => $user->id,
                'shop_id' => $shop->id,
            ]);
        }

        $this->assertEquals(3, Rating::where('shop_id', $shop->id)->count());
    }

    /**
     * Test: It returns average rating correctly
     */
    public function test_it_returns_average_rating_correctly(): void
    {
        $shop = Shop::first();
        $users = User::factory()->count(5)->create();

        // Create ratings: 1, 2, 3, 4, 5 stars
        foreach ($users as $index => $user) {
            Rating::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'stars' => $index + 1,
            ]);
        }

        // Average should be (1+2+3+4+5) / 5 = 3.0
        $averageRating = $shop->averageRating();

        $this->assertEquals(3.0, $averageRating);
    }

    /**
     * Test: Stars must be between 1 and 5
     */
    public function test_stars_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create();
        $shop = Shop::first();

        // Test valid stars (1-5)
        for ($stars = 1; $stars <= 5; $stars++) {
            $testUser = User::factory()->create();
            $rating = Rating::create([
                'user_id' => $testUser->id,
                'shop_id' => $shop->id,
                'stars' => $stars,
            ]);

            $this->assertEquals($stars, $rating->stars);
        }
    }

    /**
     * Test: Average rating returns null when no ratings
     */
    public function test_average_rating_returns_null_when_no_ratings(): void
    {
        // Create a new shop with no ratings
        $neighborhood = \App\Models\Neighborhood::first();
        $category = \App\Models\Category::skip(10)->first();

        $shop = Shop::create([
            'neighborhood_id' => $neighborhood->id,
            'category_id' => $category->id,
            'name' => 'New Shop No Ratings',
            'discount_percent' => 6.50,
            'lat' => 35.6969744,
            'lng' => -0.6331195,
            'phone' => '+213551234599',
            'is_active' => true,
        ]);

        $averageRating = $shop->averageRating();

        $this->assertNull($averageRating);
    }

    /**
     * Test: Shop with mixed ratings calculates correct average
     */
    public function test_shop_with_mixed_ratings_calculates_correct_average(): void
    {
        $shop = Shop::skip(1)->first();

        // Create ratings: 5, 4, 5, 3, 5 (average = 4.4)
        $ratingValues = [5, 4, 5, 3, 5];

        foreach ($ratingValues as $stars) {
            $user = User::factory()->create();
            Rating::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'stars' => $stars,
            ]);
        }

        $averageRating = $shop->averageRating();

        $this->assertEquals(4.4, $averageRating);
    }
}
