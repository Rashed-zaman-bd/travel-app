<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaDocumentRequest;
use App\Http\Resources\VisaDocumentCategoryResource;
use App\Http\Resources\VisaDocumentResource;
use App\Models\VisaDocument;
use App\Models\VisaDocumentCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaDocumentController extends Controller
{
    /** Admin list with filters */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = VisaDocument::query()
            ->with(['visaCountry:id,name', 'visaType:id,name', 'category:id,title'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->string('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('document_name->en', 'like', $term)
                      ->orWhere('document_name->bn', 'like', $term);
                });
            })
            ->when($request->filled('visa_country_id'), fn ($q) =>
                $q->where('visa_country_id', $request->integer('visa_country_id'))
            )
            ->when($request->filled('visa_type_id'), fn ($q) =>
                $q->where('visa_type_id', $request->integer('visa_type_id'))
            )
            ->when($request->filled('visa_document_category_id'), fn ($q) =>
                $q->where('visa_document_category_id', $request->integer('visa_document_category_id'))
            )
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('visa_document_category_id')
            ->orderBy('sort_order')
            ->orderBy('id');

        $perPage = min((int) $request->get('per_page', 15), 100);

        return VisaDocumentResource::collection(
            $request->boolean('paginate', true)
                ? $query->paginate($perPage)
                : $query->get()
        );
    }

    /**
     * Public: categories with their documents for a country (+ optional type).
     * GET /visa-documents/grouped?visa_country_id=1&visa_type_id=2
     */
    public function grouped(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'visa_country_id' => ['required', 'integer'],
            'visa_type_id'    => ['nullable', 'integer'],
        ]);

        $scope = fn ($q) => $q
            ->where('is_active', true)
            ->where('visa_country_id', $request->integer('visa_country_id'))
            ->when($request->filled('visa_type_id'), fn ($q) =>
                $q->where('visa_type_id', $request->integer('visa_type_id'))
            );

        $categories = VisaDocumentCategory::query()
            ->where('is_active', true)
            ->whereHas('documents', $scope)   // hide categories with no documents
            ->with(['documents' => $scope])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return VisaDocumentCategoryResource::collection($categories);
    }

    public function store(VisaDocumentRequest $request): JsonResponse
    {
        $document = VisaDocument::create($request->validated());

        return (new VisaDocumentResource(
            $document->load(['visaCountry:id,name', 'visaType:id,name', 'category:id,title'])
        ))->response()->setStatusCode(201);
    }

    public function show(VisaDocument $visaDocument): VisaDocumentResource
    {
        return new VisaDocumentResource(
            $visaDocument->load(['visaCountry:id,name', 'visaType:id,name', 'category:id,title'])
        );
    }

    public function update(VisaDocumentRequest $request, VisaDocument $visaDocument): VisaDocumentResource
    {
        $visaDocument->update($request->validated());

        return new VisaDocumentResource(
            $visaDocument->fresh(['visaCountry:id,name', 'visaType:id,name', 'category:id,title'])
        );
    }

    public function destroy(VisaDocument $visaDocument): JsonResponse
    {
        $visaDocument->delete();

        return response()->json(null, 204);
    }
}
