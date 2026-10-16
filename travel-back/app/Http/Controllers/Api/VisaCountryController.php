<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisaCountryRequest;
use App\Http\Resources\VisaCountryResource;
use App\Models\VisaCountry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class VisaCountryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = VisaCountry::query()
            ->when(!$request->user('sanctum'), fn ($q) => $q->where('is_active', true)) // public sees active only
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->input('search') . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name->en', 'like', $term)
                    ->orWhere('name->bn', 'like', $term);
                });
            })
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($request->boolean('all')) {
            return VisaCountryResource::collection($query->get());
        }

        return VisaCountryResource::collection(
            $query->paginate($request->integer('per_page', 15))
        );
    }

    public function store(VisaCountryRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('visa-countries', 'public');
        }

        $data['sort_order'] ??= 0;
        $data['is_active']  ??= true;

        $country = VisaCountry::create($data); // slug generated in model

        return (new VisaCountryResource($country))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VisaCountry $visaCountry): VisaCountryResource
    {
        return new VisaCountryResource($visaCountry->load('visaServices'));
    }

    public function update(VisaCountryRequest $request, VisaCountry $visaCountry): VisaCountryResource
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteImage($visaCountry->image);
            $data['image'] = $request->file('image')->store('visa-countries', 'public');
        } else {
            unset($data['image']); // keep the existing image
        }

        $visaCountry->update($data);

        return new VisaCountryResource($visaCountry->fresh());
    }

    public function destroy(VisaCountry $visaCountry): JsonResponse
    {
        if ($visaCountry->visaServices()->exists()) {
            return response()->json([
                'message' => 'This country has visa services. Delete or move them first.',
            ], 422);
        }

        $this->deleteImage($visaCountry->image);
        $visaCountry->delete();

        return response()->json(['message' => 'Visa country deleted.']);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && !filter_var($path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($path);
        }
    }
}