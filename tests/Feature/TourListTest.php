<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Travel;
use App\Models\Tour;

class TourListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * feature test for paginated tour list
     */
    public function test_tour_paginated_list(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        Tour::factory(20)->create(['travel_id' => $travelId]);
        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';
        $response = $this->get($route);

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    /**
     * feature test for tour price if retrieved correctly
     */
    public function test_tour_price_if_shown_correctly(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        Tour::factory()->create(['travel_id' => $travelId, 'price' => 123.45]);
        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';
        $response = $this->get($route);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '123.45']);
    }
}
