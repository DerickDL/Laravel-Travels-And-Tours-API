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

    /**
     * Feature test if tour is sorted correctly by starting date
     */
    public function test_tour_order_by_starting_date_in_ascending_order_correctly(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $firstTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01']);
        $secondTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-10']);
        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route);

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $firstTour->id);
        $response->assertJsonPath('data.1.id', $secondTour->id);
    }

     /**
     * Feature test if tour is sorted correctly by price in ascending order
     */
    public function test_tour_order_by_price_in_ascending_order_correctly(): void 
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $cheapButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 100]);
        $cheapButLateTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-10', 'price' => 100]);
        $expensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 5000]);
        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapButEarlyTour->id);
        $response->assertJsonPath('data.1.id', $cheapButLateTour->id);
        $response->assertJsonPath('data.2.id', $expensiveButEarlyTour->id);
    }

    /**
     * Feature test if tour is sorted correctly by price in descending order
     */
    public function test_tour_order_by_price_in_descending_order_correctly(): void 
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $cheapButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 100]);
        $cheapButLateTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-10', 'price' => 100]);
        $expensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 5000]);
        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?sortBy=price&sortOrder=desc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $expensiveButEarlyTour->id);
        $response->assertJsonPath('data.1.id', $cheapButEarlyTour->id);
        $response->assertJsonPath('data.2.id', $cheapButLateTour->id);
    }

    /**
     * Feature test if tour between priceFrom and priceTo retreived correctly and in ascending order by price
     */
    public function test_tour_retrieve_prices_from_100_to_500_only_and_order_by_price_in_ascending_order(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $under500ButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 100]);
        $under500ButLateTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-10', 'price' => 100]);
        $under500ExpensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-05', 'price' => 300]);
        $exact500ExpensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 500]);
        $over500ExpensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-06-01', 'price' => 600]);

        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?priceFrom=100&priceTo=500&sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $under500ButEarlyTour->id);
        $response->assertJsonPath('data.1.id', $under500ButLateTour->id);
        $response->assertJsonPath('data.2.id', $under500ExpensiveButEarlyTour->id);
        $response->assertJsonPath('data.3.id', $exact500ExpensiveButEarlyTour->id);
        $response->assertJsonMissing(['id'=> $over500ExpensiveButEarlyTour->id]);
    }

        /**
     * Feature test if tour between priceFrom and priceTo retreived correctly and in descending order by price
     */
    public function test_tour_retrieve_prices_from_100_to_500_only_and_order_by_price_in_descending_order(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $under500ButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 100]);
        $under500ButLateTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-10', 'price' => 100]);
        $under500ExpensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-05', 'price' => 300]);
        $exact500ExpensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'price' => 500]);
        $over500ExpensiveButEarlyTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-06-01', 'price' => 600]);

        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?priceFrom=100&priceTo=500&sortBy=price&sortOrder=desc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $exact500ExpensiveButEarlyTour->id);
        $response->assertJsonPath('data.1.id', $under500ExpensiveButEarlyTour->id);
        $response->assertJsonPath('data.2.id', $under500ButEarlyTour->id);
        $response->assertJsonPath('data.3.id', $under500ButLateTour->id);
        $response->assertJsonMissing(['id'=> $over500ExpensiveButEarlyTour->id]);
    }

    /**
     * Feature test to get all tours between 2023-07-01 to 2023-07-10 only
     */
    public function test_tour_retrieve_list_between_starting_date_and_ending_date(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $firstTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'ending_date' => '2023-07-03']);
        $secondTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-05', 'ending_date' => '2023-07-07']);
        $thirdTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-09', 'ending_date' => '2023-07-10']);
        $fourthButOverTour = Tour::factory()->create(['travel_id' => $travelId, 'starting_date' => '2023-07-01', 'ending_date' => '2023-07-15']);

        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?dateFrom=2023-07-01&dateTo=2023-07-10');
        
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $firstTour->id);
        $response->assertJsonPath('data.1.id', $secondTour->id);
        $response->assertJsonPath('data.2.id', $thirdTour->id);
        $response->assertJsonMissing(['id'=> $fourthButOverTour->id]);
    }

    /**
     * Feature test to get tours with prices between 100 to 900 and dates between 2023-07-01 to 2023-07-20
     */
    public function test_tour_retrieve_only_prices_between_100_to_900_and_dates_between_20230701_to_20230720(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $firstTourPrice100 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-01', 
            'ending_date' => '2023-07-03', 
            'price' => 100
        ]);
        $secondTourPrice900 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-05', 
            'ending_date' => '2023-07-07', 
            'price' => 900
        ]);
        $thirdTourPrice300 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-08', 
            'ending_date' => '2023-07-10', 
            'price' => 100
        ]);
        $fourthTourPrice400 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-09', 
            'ending_date' => '2023-07-07', 
            'price' => 400
        ]);
        $fifthTourPrice1200 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-09', 
            'ending_date' => '2023-07-07', 
            'price' => 1200
        ]);
        $sixthTourPrice400 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-09', 
            'ending_date' => '2023-07-21', 
            'price' => 400
        ]);

        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?dateFrom=2023-07-01&dateTo=2023-07-10&priceFrom=100&priceTo=900');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $firstTourPrice100->id);
        $response->assertJsonPath('data.1.id', $secondTourPrice900->id);
        $response->assertJsonPath('data.2.id', $thirdTourPrice300->id);
        $response->assertJsonPath('data.3.id', $fourthTourPrice400->id);
        $response->assertJsonMissing(['id'=> $fifthTourPrice1200->id]);
        $response->assertJsonMissing(['id'=> $sixthTourPrice400->id]);
    }

    /**
     * Feature test to get tours with prices between 100 to 900 and dates between 2023-07-01 to 2023-07-20
     * Sorted in ascending order
     */
    public function test_tour_retrieve_only_prices_between_100_to_900_and_dates_between_20230701_to_20230720_sorted_by_price_in_ascending_order(): void
    {
        $travel = Travel::factory(1)->create(['is_public' => true]);
        $travelId = $travel->first()->id;
        $firstTourPrice100 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-01', 
            'ending_date' => '2023-07-03', 
            'price' => 100
        ]);
        $secondTourPrice900 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-05', 
            'ending_date' => '2023-07-07', 
            'price' => 900
        ]);
        $thirdTourPrice300 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-08', 
            'ending_date' => '2023-07-10', 
            'price' => 100
        ]);
        $fourthTourPrice400 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-09', 
            'ending_date' => '2023-07-07', 
            'price' => 400
        ]);
        $fifthTourPrice1200 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-09', 
            'ending_date' => '2023-07-07', 
            'price' => 1200
        ]);
        $sixthTourPrice400 = Tour::factory()->create([
            'travel_id' => $travelId, 
            'starting_date' => '2023-07-09', 
            'ending_date' => '2023-07-21', 
            'price' => 400
        ]);

        $travelSlug = $travel->first()->slug;
        $route = '/api/v1/travel/' . $travelSlug . '/tours';

        $response = $this->get($route . '?dateFrom=2023-07-01&dateTo=2023-07-10&priceFrom=100&priceTo=900&sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $firstTourPrice100->id);
        $response->assertJsonPath('data.1.id', $thirdTourPrice300->id);
        $response->assertJsonPath('data.2.id', $fourthTourPrice400->id);
        $response->assertJsonPath('data.3.id', $secondTourPrice900->id);
        $response->assertJsonMissing(['id'=> $fifthTourPrice1200->id]);
        $response->assertJsonMissing(['id'=> $sixthTourPrice400->id]);
    }
}
