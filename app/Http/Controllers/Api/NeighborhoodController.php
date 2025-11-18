<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NeighborhoodResource;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    /**
     * Get all neighborhoods
     */
    public function index(Request $request)
    {
        $neighborhoods = Neighborhood::all();
        
        return NeighborhoodResource::collection($neighborhoods);
    }
}
