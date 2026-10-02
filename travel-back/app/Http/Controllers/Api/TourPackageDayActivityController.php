<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageDayActivityRequest;
use App\Http\Resources\TourPackageDayActivityResource;
use App\Models\TourPackageDayActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class TourPackageDayActivityController extends Controller
{
    /**
     * List activities (admin sees inactive ones too).
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $activities = TourPackageDayActivity::query()
            ->with(['day', 'tourPackage'])
            ->when(
                $request->filled('tour_package_id'),
                fn ($q) => $q->where('tour_package_id', $request->tour_package_id)
            )
            ->when(
                $request->filled('tour_package_itinerary_id'),
                fn ($q) => $q->where('tour_package_itinerary_id', $request->tour_package_itinerary_id)
            )
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return TourPackageDayActivityResource::collection($activities);
    }

    /**
     * Store a new activity.
     */
    public function store(TourPackageDayActivityRequest $request): TourPackageDayActivityResource
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('tour-package-activity/image', 'public');
        }

        $data['order']     = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        $activity = TourPackageDayActivity::create($data);

        return new TourPackageDayActivityResource(
            $activity->load(['day', 'tourPackage'])
        );
    }

    /**
     * Show one activity.
     */
    public function show(
        Request $request,
        TourPackageDayActivity $tourPackageDayActivity
    ): TourPackageDayActivityResource {
       
        return new TourPackageDayActivityResource(
            $tourPackageDayActivity->load(['day', 'tourPackage'])
        );
    }

    /**
     * Update an activity.
     */
    public function update(
        TourPackageDayActivityRequest $request,
        TourPackageDayActivity $tourPackageDayActivity
    ): TourPackageDayActivityResource {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($tourPackageDayActivity->image) {
                Storage::disk('public')->delete($tourPackageDayActivity->image);
            }

            $data['image'] = $request
                ->file('image')
                ->store('tour-package-activity/image', 'public');
        }

        $data['order']     = $data['order'] ?? $tourPackageDayActivity->order;
        $data['is_active'] = $data['is_active'] ?? $tourPackageDayActivity->is_active;

        $tourPackageDayActivity->update($data);

        return new TourPackageDayActivityResource(
            $tourPackageDayActivity->load(['day', 'tourPackage'])
        );
    }

    /**
     * Delete an activity.
     */
    public function destroy(TourPackageDayActivity $tourPackageDayActivity): JsonResponse
    {
        if ($tourPackageDayActivity->image) {
            Storage::disk('public')->delete($tourPackageDayActivity->image);
        }

        $tourPackageDayActivity->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Tour package activity deleted successfully.',
        ]);
    }
}