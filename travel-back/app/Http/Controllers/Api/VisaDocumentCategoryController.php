<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaDocumentCategoryRequest;
use App\Http\Resources\VisaDocumentCategoryResource;
use App\Models\VisaDocumentCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaDocumentCategoryController extends Controller
{
    /**
     * Admin list; also usable publicly with ?visa_service_id=..&is_active=1&with_documents=1&paginate=0
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = VisaDocumentCategory::query()
            ->with('visaService:id,title')
            ->withCount('documents')
            ->when($request->boolean('with_documents'), fn ($q) =>
                $q->with(['documents' => fn ($d) =>
                    $d->when($request->filled('is_active'), fn ($d) =>
                        $d->where('is_active', $request->boolean('is_active'))
                    )
                ])
            )
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('title->en', 'like', $term)
                      ->orWhere('title->bn', 'like', $term);
                });
            })
            ->when($request->filled('visa_service_id'), fn ($q) =>
                $q->where('visa_service_id', $request->integer('visa_service_id'))
            )
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('sort_order')
            ->orderBy('id');

        $perPage = min((int) $request->get('per_page', 15), 100);

        return VisaDocumentCategoryResource::collection(
            $request->boolean('paginate', true)
                ? $query->paginate($perPage)
                : $query->get()
        );
    }

    public function store(VisaDocumentCategoryRequest $request): JsonResponse
    {
        $category = VisaDocumentCategory::create($request->validated());

        return (new VisaDocumentCategoryResource($category->load('visaService:id,title')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VisaDocumentCategory $visaDocumentCategory): VisaDocumentCategoryResource
    {
        return new VisaDocumentCategoryResource(
            $visaDocumentCategory->load(['visaService:id,title', 'documents'])
        );
    }

    public function update(
        VisaDocumentCategoryRequest $request,
        VisaDocumentCategory $visaDocumentCategory
    ): VisaDocumentCategoryResource {
        $visaDocumentCategory->update($request->validated());

        return new VisaDocumentCategoryResource(
            $visaDocumentCategory->fresh('visaService:id,title')
        );
    }

    public function destroy(VisaDocumentCategory $visaDocumentCategory): JsonResponse
    {
        if ($visaDocumentCategory->documents()->exists()) {
            return response()->json([
                'message' => 'This category has documents. Delete or move them first.',
            ], 422);
        }

        $visaDocumentCategory->delete();

        return response()->json(null, 204);
    }
}
