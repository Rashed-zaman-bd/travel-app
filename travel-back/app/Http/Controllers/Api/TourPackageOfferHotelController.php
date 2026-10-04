<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageOfferHotelRequest;
use App\Http\Resources\TourPackageOfferHotelResource;
use App\Models\TourPackageOfferHotel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourPackageOfferHotelController extends Controller
{
    /**
     * Display hotel list.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $hotels = TourPackageOfferHotel::query()
            ->with([ 'tourPackage', 'offer'  ])

            // Filter by tour package
            ->when(
                $request->filled('tour_package_id'),
                function ($query) use ($request) {
                    $query->where(
                        'tour_package_id',
                        $request->integer('tour_package_id')
                    );
                }
            )

            // Filter by offer
            ->when(
                $request->filled('tour_package_offer_id'),
                function ($query) use ($request) {
                    $query->where(
                        'tour_package_offer_id',
                        $request->integer('tour_package_offer_id')
                    );
                }
            )

            // Active only
            ->when(
                $request->has('is_active'),
                function ($query) use ($request) {
                    $query->where(
                        'is_active',
                        $request->boolean('is_active')
                    );
                }
            )

            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return TourPackageOfferHotelResource::collection(
            $hotels
        );
    }


    /**
     * Store a new hotel.
     */
    public function store(
        TourPackageOfferHotelRequest $request
    ): TourPackageOfferHotelResource {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? 0;

        $data['is_active'] =
            $data['is_active'] ?? true;

        $hotel = TourPackageOfferHotel::create($data);

        return new TourPackageOfferHotelResource(
            $hotel->load([
                'tourPackage',
                'offer',
            ])
        );
    }


    /**
     * Display a single hotel.
     */
    public function show(
        TourPackageOfferHotel $tourPackageOfferHotel
    ): TourPackageOfferHotelResource {

        $tourPackageOfferHotel->load([ 'tourPackage', 'offer' ]);

        return new TourPackageOfferHotelResource(
            $tourPackageOfferHotel
        );
    }


    /**
     * Update hotel.
     */
    public function update(
        TourPackageOfferHotelRequest $request,
        TourPackageOfferHotel $tourPackageOfferHotel
    ): TourPackageOfferHotelResource {

        $data = $request->validated();

        $data['order'] =
            $data['order']
            ?? $tourPackageOfferHotel->order;

        $data['is_active'] =
            $data['is_active']
            ?? $tourPackageOfferHotel->is_active;

        $tourPackageOfferHotel->update($data);

        return new TourPackageOfferHotelResource(
            $tourPackageOfferHotel
                ->fresh()
                ->load([
                    'tourPackage',
                    'offer',
                ])
        );
    }


    /**
     * Delete hotel.
     */
    public function destroy(
        TourPackageOfferHotel $tourPackageOfferHotel
    ): JsonResponse {

        $tourPackageOfferHotel->delete();

        return response()->json([
            'status' => true,
            'message' =>
                'Tour package offer hotel deleted successfully.',
        ]);
    }
}