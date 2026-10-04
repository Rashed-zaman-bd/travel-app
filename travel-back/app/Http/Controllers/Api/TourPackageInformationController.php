<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageInformationRequest;
use App\Http\Resources\TourPackageInformationResource;
use App\Models\TourPackageInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourPackageInformationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $informations = TourPackageInformation::query()
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

        return TourPackageInformationResource::collection( $informations );
    }


     public function store( TourPackageInformationRequest $request ): TourPackageInformationResource
     {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? 0;

        $data['is_active'] = $data['is_active'] ?? true;

        $informations = TourPackageInformation::create($data);

        return new TourPackageInformationResource(
            $informations->load('tourPackage')
        );
    }


     public function show( TourPackageInformation $tourPackageInformation ): TourPackageInformationResource 
     {

        $tourPackageInformation->load('tourPackage');

        return new TourPackageInformationResource(
            $tourPackageInformation
        );
    }


    public function update( TourPackageInformationRequest $request, TourPackageInformation $tourPackageInformation
    ): TourPackageInformationResource {

        $data = $request->validated();

        $data['order'] = $data['order'] ?? $tourPackageInformation->order;

        $data['is_active'] =
            $data['is_active']
            ?? $tourPackageInformation->is_active;

        $tourPackageInformation->update($data);

        return new TourPackageInformationResource(
            $tourPackageInformation->fresh()->load('tourPackage')
        );
    }


    public function destroy(
        TourPackageInformation $tourPackageInformation
    ): JsonResponse {

        $tourPackageInformation->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tour package information deleted successfully.',
        ]);
    }
}
