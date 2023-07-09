<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Travel;
use App\Http\Resources\TourResource;

class TourController extends Controller
{
    /**
     * Get all tours under travel slug
     */
    public function index(Travel $travel)
    {
        // return Travel::where('slug', $travel)->find(1)->tours()
        // ->where('price', '>', 500)
        // ->paginate(10);
        $tours = $travel->tours()
                        ->orderBy('starting_date')
                        ->paginate(10);
        return TourResource::collection($tours);
    }
}
