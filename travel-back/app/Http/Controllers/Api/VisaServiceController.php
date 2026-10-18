<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaServiceRequest;
use App\Http\Resources\VisaServiceResource;
use App\Models\VisaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaServiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = VisaService::query()
            ->with(['visaCountry', 'visaType'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('title->en', 'like', $term)
                      ->orWhere('title->bn', 'like', $term);
                });
            })
            ->when($request->filled('visa_country_id'), fn ($q) =>
                $q->where('visa_country_id', $request->integer('visa_country_id'))
            )
            ->when($request->filled('visa_type_id'), fn ($q) =>
                $q->where('visa_type_id', $request->integer('visa_type_id'))
            )
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->latest('id');

        $perPage = min((int) $request->get('per_page', 15), 100);

        return VisaServiceResource::collection(
            $request->boolean('paginate', true)
                ? $query->paginate($perPage)
                : $query->get()
        );
    }

    public function store(VisaServiceRequest $request): JsonResponse
    {
        $service = VisaService::create($request->validated());

        return (new VisaServiceResource($service->load(['visaCountry', 'visaType'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VisaService $visaService): VisaServiceResource
    {
        return new VisaServiceResource($visaService->load(['visaCountry', 'visaType']));
    }

    public function update(VisaServiceRequest $request, VisaService $visaService): VisaServiceResource
    {
        $visaService->update($request->validated());

        return new VisaServiceResource($visaService->fresh(['visaCountry', 'visaType']));
    }

    public function destroy(VisaService $visaService): JsonResponse
    {
        if ($visaService->visaAssistances()->exists()) {
            return response()->json([
                'message' => 'This service has assistance requests. It cannot be deleted.',
            ], 422);
        }

        $visaService->delete();

        return response()->json(null, 204);
    }
}