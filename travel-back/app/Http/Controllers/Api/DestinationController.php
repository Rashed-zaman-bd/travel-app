<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestinationRequest;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    /**
     * Display all destinations.
     */
    public function index(Request $request)
    {
        $destinations = Destination::latest()->get();

        return DestinationResource::collection($destinations);
    }

    /**
     * Store a new destination.
     */
    public function store(DestinationRequest $request): DestinationResource
    {
        $data = $request->validated();

        // Generate slug if it is empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug(
                $data['title']['en'] ?? $data['destination_name']['en']
            );
        }

        // Upload main image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('destinations', 'public');
        }

        // Upload hero image
        if ($request->hasFile('destination_hero_image')) {
            $data['destination_hero_image'] = $request
                ->file('destination_hero_image')
                ->store('destinations/hero', 'public');
        }

        // Upload map image
        if ($request->hasFile('map_image')) {
            $data['map_image'] = $request
                ->file('map_image')
                ->store('destinations/maps', 'public');
        }

        $destination = Destination::create($data);

        return new DestinationResource($destination);
    }

    /**
     * Display a single destination.
     */
    public function show(Destination $destination): DestinationResource
    {
        return new DestinationResource($destination);
    }

    /**
     * Update a destination.
     */
    public function update(
        DestinationRequest $request,
        Destination $destination
    ): DestinationResource {
        $data = $request->validated();

        // Main image
        if ($request->hasFile('image')) {

            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }

            $data['image'] = $request->file('image')
                ->store('destinations', 'public');
        }

        // Hero image
        if ($request->hasFile('destination_hero_image')) {

            if ($destination->destination_hero_image) {
                Storage::disk('public')->delete(
                    $destination->destination_hero_image
                );
            }

            $data['destination_hero_image'] = $request
                ->file('destination_hero_image')
                ->store('destinations/hero', 'public');
        }

        // Map image
        if ($request->hasFile('map_image')) {

            if ($destination->map_image) {
                Storage::disk('public')->delete($destination->map_image);
            }

            $data['map_image'] = $request
                ->file('map_image')
                ->store('destinations/maps', 'public');
        }

        $destination->update($data);

        return new DestinationResource($destination->fresh());
    }

    /**
     * Delete a destination.
     */
    public function destroy(Destination $destination): JsonResponse
    {
        // Delete main image
        if ($destination->image) {
            Storage::disk('public')->delete($destination->image);
        }

        // Delete hero image
        if ($destination->destination_hero_image) {
            Storage::disk('public')->delete(
                $destination->destination_hero_image
            );
        }

        // Delete map image
        if ($destination->map_image) {
            Storage::disk('public')->delete($destination->map_image);
        }

        $destination->delete();

        return response()->json([
            'status' => true,
            'message' => 'Destination deleted successfully.',
        ]);
    }
}
