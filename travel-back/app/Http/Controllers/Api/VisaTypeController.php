<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaTypeRequest;
use App\Http\Resources\VisaTypeResource;
use App\Models\VisaType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaTypeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = VisaType::query()
            ->withCount('visaServices')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name->en', 'like', $term)
                      ->orWhere('name->bn', 'like', $term);
                });
            })
            ->when($request->has('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('sort_order')
            ->orderBy('id');

        $perPage = min((int) $request->get('per_page', 15), 100);

        return VisaTypeResource::collection(
            $request->boolean('paginate', true)
                ? $query->paginate($perPage)
                : $query->get()
        );
    }

    public function store(VisaTypeRequest $request): JsonResponse
    {
        $visaType = VisaType::create($request->validated());

        return (new VisaTypeResource($visaType))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VisaType $visaType): VisaTypeResource
    {
        return new VisaTypeResource($visaType->load('visaServices'));
    }

    public function update(VisaTypeRequest $request, VisaType $visaType): VisaTypeResource
    {
        $visaType->update($request->validated());

        return new VisaTypeResource($visaType->fresh());
    }

    public function destroy(VisaType $visaType): JsonResponse
    {
        if ($visaType->visaServices()->exists()) {
            return response()->json([
                'message' => 'This visa type has services attached. Remove or reassign them first.',
            ], 422);
        }

        $visaType->delete();

        return response()->json(null, 204);
    }
}
