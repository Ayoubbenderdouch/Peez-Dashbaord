<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class AdminNeighborhoodController extends Controller
{
    /**
     * Get All Neighborhoods
     */
    public function index(Request $request)
    {
        $query = Neighborhood::query();

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $perPage = $request->get('per_page', 50);
        $neighborhoods = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => $neighborhoods->map(function ($neighborhood) {
                return [
                    'id' => $neighborhood->id,
                    'name' => $neighborhood->name,
                    'city' => $neighborhood->city,
                    'shopsCount' => $neighborhood->shops()->count(),
                    'createdAt' => $neighborhood->created_at->toIso8601String(),
                ];
            }),
            'meta' => [
                'current_page' => $neighborhoods->currentPage(),
                'last_page' => $neighborhoods->lastPage(),
                'per_page' => $neighborhoods->perPage(),
                'total' => $neighborhoods->total(),
            ],
        ]);
    }

    /**
     * Get Single Neighborhood
     */
    public function show($id)
    {
        $neighborhood = Neighborhood::find($id);

        if (!$neighborhood) {
            return response()->json([
                'message' => 'Neighborhood not found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $neighborhood->id,
                'name' => $neighborhood->name,
                'city' => $neighborhood->city,
                'shopsCount' => $neighborhood->shops()->count(),
                'createdAt' => $neighborhood->created_at->toIso8601String(),
                'updatedAt' => $neighborhood->updated_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Create New Neighborhood
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:neighborhoods,name',
            'city' => 'nullable|string|max:100',
        ]);

        $neighborhood = Neighborhood::create([
            'name' => $validated['name'],
            'city' => $validated['city'] ?? 'Oran',
        ]);

        return response()->json([
            'message' => 'Neighborhood created successfully',
            'data' => [
                'id' => $neighborhood->id,
                'name' => $neighborhood->name,
                'city' => $neighborhood->city,
                'createdAt' => $neighborhood->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Update Neighborhood
     */
    public function update(Request $request, $id)
    {
        $neighborhood = Neighborhood::find($id);

        if (!$neighborhood) {
            return response()->json([
                'message' => 'Neighborhood not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:neighborhoods,name,' . $id,
            'city' => 'nullable|string|max:100',
        ]);

        $neighborhood->update($validated);

        return response()->json([
            'message' => 'Neighborhood updated successfully',
            'data' => [
                'id' => $neighborhood->id,
                'name' => $neighborhood->name,
                'city' => $neighborhood->city,
            ],
        ]);
    }

    /**
     * Delete Neighborhood
     */
    public function destroy($id)
    {
        $neighborhood = Neighborhood::find($id);

        if (!$neighborhood) {
            return response()->json([
                'message' => 'Neighborhood not found',
            ], 404);
        }

        // Check if neighborhood has shops
        $shopsCount = $neighborhood->shops()->count();
        if ($shopsCount > 0) {
            return response()->json([
                'message' => "Cannot delete neighborhood with {$shopsCount} shop(s). Please delete or reassign shops first.",
            ], 422);
        }

        $neighborhoodName = $neighborhood->name;
        $neighborhood->delete();

        return response()->json([
            'message' => "Neighborhood '{$neighborhoodName}' deleted successfully",
        ]);
    }
}
