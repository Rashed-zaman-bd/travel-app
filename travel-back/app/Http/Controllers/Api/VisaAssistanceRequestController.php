<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaAssistanceRequestRequest;
use App\Http\Resources\VisaAssistanceRequestResource;
use App\Models\VisaAssistanceRequest;
use App\Models\VisaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaAssistanceRequestController extends Controller
{
    /** Admin: list with filters */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = VisaAssistanceRequest::query()
            ->with('visaService:id,title')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('service_name', 'like', $term);
                });
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->string('status'))
            )
            ->when($request->filled('visa_service_id'), fn ($q) =>
                $q->where('visa_service_id', $request->integer('visa_service_id'))
            )
            ->latest('id');

        $perPage = min((int) $request->get('per_page', 15), 100);

        return VisaAssistanceRequestResource::collection($query->paginate($perPage));
    }

    /** Public: submit the assistance form */
    public function store(VisaAssistanceRequestRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Keep a readable service name, because visa_service_id becomes null if the service is deleted
        if (! empty($data['visa_service_id'])) {
            $service = VisaService::find($data['visa_service_id']);
            $data['service_name'] = $service?->title['en'] ?? null;
        }

        VisaAssistanceRequest::create($data); // status defaults to "pending"

        return response()->json(['message' => 'Request submitted.'], 201);
    }

    /** Admin: single request */
    public function show(VisaAssistanceRequest $visaAssistanceRequest): VisaAssistanceRequestResource
    {
        return new VisaAssistanceRequestResource($visaAssistanceRequest->load('visaService:id,title'));
    }

    /** Admin: change status or fix contact details */
    public function update(
        VisaAssistanceRequestRequest $request,
        VisaAssistanceRequest $visaAssistanceRequest
    ): VisaAssistanceRequestResource {
        $visaAssistanceRequest->update($request->validated());

        return new VisaAssistanceRequestResource(
            $visaAssistanceRequest->fresh('visaService:id,title')
        );
    }

    /** Admin: delete */
    public function destroy(VisaAssistanceRequest $visaAssistanceRequest): JsonResponse
    {
        $visaAssistanceRequest->delete();

        return response()->json(null, 204);
    }
}
