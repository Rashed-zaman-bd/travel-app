<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageHighlightRequest;
use App\Http\Resources\TourPackageHighlightResource;
use App\Models\TourPackageHighlight;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class TourPackageHighlightController extends Controller
{
    /**
     * Display a listing of highlights.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $highlights = TourPackageHighlight::query()
            ->with('tourPackage')
            ->when(
                $request->filled('tour_package_id'),
                function ($query) use ($request) {
                    $query->where(
                        'tour_package_id',
                        $request->tour_package_id
                    );
                }
            )
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return TourPackageHighlightResource::collection(
            $highlights
        );
    }

    /**
     * Store a newly created highlight.
     */
    public function store(
        TourPackageHighlightRequest $request
    ): TourPackageHighlightResource {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? 0;

        $data['is_active'] = $data['is_active'] ?? true;

        $highlight = TourPackageHighlight::create($data);

        return new TourPackageHighlightResource(
            $highlight->load('tourPackage')
        );
    }

    /**
     * Display the specified highlight.
     */
    public function show(
        TourPackageHighlight $tourPackageHighlight
    ): TourPackageHighlightResource {

        $tourPackageHighlight->load('tourPackage');

        return new TourPackageHighlightResource(
            $tourPackageHighlight
        );
    }

    /**
     * Update the specified highlight.
     */
    public function update(
        TourPackageHighlightRequest $request,
        TourPackageHighlight $tourPackageHighlight
    ): TourPackageHighlightResource {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? $tourPackageHighlight->order;

        $data['is_active'] =
            $data['is_active']
            ?? $tourPackageHighlight->is_active;

        $tourPackageHighlight->update($data);

        return new TourPackageHighlightResource(
            $tourPackageHighlight->fresh()->load('tourPackage')
        );
    }

    /**
     * Remove the specified highlight.
     */
    public function destroy(
        TourPackageHighlight $tourPackageHighlight
    ): JsonResponse {

        $tourPackageHighlight->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tour package highlight deleted successfully.',
        ]);
    }
}