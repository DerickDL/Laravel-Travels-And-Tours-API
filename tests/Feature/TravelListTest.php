<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Travel;

class TravelListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * feature test for paginated travel list
     */
    public function test_travel_paginated_list(): void
    {
        Travel::factory(30)->create(['is_public' => true]);
        
        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 3);
    }

    /**
     * feature test for paginated travel list
     */
    public function test_travel_number_of_nights_if_shown_correctly(): void
    {
        Travel::factory()->create(['is_public' => true, 'number_of_days' => 5]);
        
        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);
        $response->assertJsonFragment(['number_of_nights' => 4]);
    }
}
