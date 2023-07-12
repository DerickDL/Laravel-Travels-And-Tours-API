<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Travel;
use App\Http\Resources\TourResource;
use App\Http\Requests\TourListRequest;

class TourController extends Controller
{
    /**
     * Get all tours under travel slug
     * priceTo, priceFrom, dateFrom, dateTo, orderBy(key, asc/desc)
     */
    public function index(Travel $travel, TourListRequest $request)
    {
        $tours = $travel->tours()
                        ->when($request->priceFrom, function ($query) use ($request) {
                            return $query->where('price', '>=', $request->priceFrom * 100);
                        })
                        ->when($request->priceTo, function ($query) use ($request) {
                            return $query->where('price', '<=', $request->priceTo * 100);
                        })
                        ->when($request->dateFrom, function ($query) use ($request) {
                            return $query->where('starting_date', '>=', $request->dateFrom);
                        })
                        ->when($request->dateTo, function ($query) use ($request) {
                            return $query->where('ending_date', '<=', $request->dateTo);
                        })
                        ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
                            return $query->orderBy($request->sortBy, $request->sortOrder);
                        })
                        ->orderBy('starting_date')
                        ->paginate(10);
                        
        return TourResource::collection($tours);
    }
}
