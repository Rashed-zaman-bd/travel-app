<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageOfferRequest;
use App\Http\Resources\TourPackageOfferResource;
use App\Models\TourPackageOffer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourPackageOfferController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $offers = TourPackageOffer::query()
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

        return TourPackageOfferResource::collection(
            $offers
        );
    }


    public function store(
        TourPackageOfferRequest $request
    ): TourPackageOfferResource {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? 0;

        $data['is_active'] = $data['is_active'] ?? true;

        $offers = TourPackageOffer::create($data);

        return new TourPackageOfferResource(
            $offers->load('tourPackage')
        );
    }


    public function show(
        TourPackageOffer $tourPackageOffer
    ): TourPackageOfferResource {

        $tourPackageOffer->load('tourPackage');

        return new TourPackageOfferResource(
            $tourPackageOffer
        );
    }


        public function update(
        TourPackageOfferRequest $request,
        TourPackageOffer $tourPackageOffer
    ): TourPackageOfferResource {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? $tourPackageOffer->order;

        $data['is_active'] =
            $data['is_active']
            ?? $tourPackageOffer->is_active;

        $tourPackageOffer->update($data);

        return new TourPackageOfferResource(
            $tourPackageOffer->fresh()->load('tourPackage')
        );
    }


    public function destroy(
        TourPackageOffer $tourPackageOffer
    ): JsonResponse {

        $tourPackageOffer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tour package offer deleted successfully.',
        ]);
    }
}
