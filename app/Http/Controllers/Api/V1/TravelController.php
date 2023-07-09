<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Travel;
use App\Http\Resources\TravelResource;

class TravelController extends Controller
{
    /**
     * Get all public travels
     */
    public function index()
    {
        return TravelResource::collection(Travel::where('is_public', true)->paginate(10));
    }
}