<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageItineraryRequest;
use App\Http\Resources\TourPackageItineraryResource;
use App\Models\TourPackageItinerary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class TourPackageItineraryController extends Controller
{
    /**
     * List itineraries (admin sees inactive ones too).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        
        $itineraries = TourPackageItinerary::query()
            ->with(['tourPackage', 'activities'])
            ->when(
                $request->filled('tour_package_id'),
                fn ($q) => $q->where('tour_package_id', $request->tour_package_id)
            )
            ->orderBy('day_number')
            ->orderBy('order')
            ->get();

        return TourPackageItineraryResource::collection($itineraries);
    }

    /**
     * Store a new itinerary day.
     */
    public function store(TourPackageItineraryRequest $request): TourPackageItineraryResource
    {
        $data = $request->validated();

        if ($request->hasFile('map_image')) {
            $data['map_image'] = $request
                ->file('map_image')
                ->store('tour-package-itinerary/map', 'public');
        }

        $data['order']     = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        $itinerary = TourPackageItinerary::create($data);

        return new TourPackageItineraryResource(
            $itinerary->load(['tourPackage', 'activities'])
        );
    }

    /**
     * Show one itinerary day.
     */
    public function show(TourPackageItinerary $tourPackageItinerary): TourPackageItineraryResource
    {
        return new TourPackageItineraryResource(
            $tourPackageItinerary->load(['tourPackage', 'activities'])
        );
    }

    /**
     * Update an itinerary day.
     */
    public function update(
        TourPackageItineraryRequest $request,
        TourPackageItinerary $tourPackageItinerary
    ): TourPackageItineraryResource {
        $data = $request->validated();

        if ($request->hasFile('map_image')) {
            if ($tourPackageItinerary->map_image) {
                Storage::disk('public')->delete($tourPackageItinerary->map_image);
            }

            $data['map_image'] = $request
                ->file('map_image')
                ->store('tour-package-itinerary/map', 'public');
        }

        $data['order']     = $data['order'] ?? $tourPackageItinerary->order;
        $data['is_active'] = $data['is_active'] ?? $tourPackageItinerary->is_active;

        $tourPackageItinerary->update($data);

        return new TourPackageItineraryResource(
            $tourPackageItinerary->load(['tourPackage', 'activities'])
        );
    }

    /**
     * Delete an itinerary day.
     */
    public function destroy(TourPackageItinerary $tourPackageItinerary): JsonResponse
    {

        if ($tourPackageItinerary->map_image) {
            Storage::disk('public')->delete(
                $tourPackageItinerary->map_image
            );
        }
        $tourPackageItinerary->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Tour package itinerary deleted successfully.',
        ]);
    }
}