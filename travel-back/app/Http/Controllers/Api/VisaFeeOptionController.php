<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaFeeOptionRequest;
use App\Http\Resources\VisaFeeOptionResource;
use App\Models\VisaFeeOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaFeeOptionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $options = VisaFeeOption::query()
            ->with(['visaCountry', 'visaType'])
            ->when($request->filled('visa_country_id'), fn ($q) =>
                $q->where('visa_country_id', $request->integer('visa_country_id')))
            ->when($request->filled('visa_type_id'), fn ($q) =>
                $q->where('visa_type_id', $request->integer('visa_type_id')))
            ->when($request->has('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active')))
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return VisaFeeOptionResource::collection($options);
    }

    public function store(VisaFeeOptionRequest $request): JsonResponse
    {
        $option = VisaFeeOption::create($request->validated());

        return (new VisaFeeOptionResource($option->load(['visaCountry', 'visaType'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VisaFeeOption $visaFeeOption): VisaFeeOptionResource
    {
        return new VisaFeeOptionResource($visaFeeOption->load(['visaCountry', 'visaType']));
    }

    public function update(VisaFeeOptionRequest $request, VisaFeeOption $visaFeeOption): VisaFeeOptionResource
    {
        $visaFeeOption->update($request->validated());

        return new VisaFeeOptionResource($visaFeeOption->fresh(['visaCountry', 'visaType']));
    }

    public function destroy(VisaFeeOption $visaFeeOption): JsonResponse
    {
        $visaFeeOption->delete();

        return response()->json(['message' => 'Visa fee option deleted successfully.']);
    }
}
