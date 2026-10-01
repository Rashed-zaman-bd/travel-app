<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageRequest;
use App\Http\Resources\TourPackageResource;
use App\Models\Category;
use App\Models\TourPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class TourPackageController extends Controller
{
    /**
     * Display all tour packages.
     */
    public function index(): AnonymousResourceCollection
    {
        $tourPackages = TourPackage::query()
            ->with('category')
            ->orderBy('order', 'asc')
            ->get();

        return TourPackageResource::collection($tourPackages);
    }

    /**
     * Store a new tour package.
     */
    public function store(
        TourPackageRequest $request
    ): TourPackageResource {

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Hero Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request
                ->file('hero_image')
                ->store('tour-package/hero', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Package Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('package_image')) {
            $data['package_image'] = $request
                ->file('package_image')
                ->store('tour-package/package', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Package Map Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('package_map_image')) {
            $data['package_map_image'] = $request
                ->file('package_map_image')
                ->store('tour-package/map', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        |
        | The TourPackage model will automatically generate
        | the slug from package_name.en.
        |
        */
        $tourPackage = TourPackage::create($data);

        return new TourPackageResource(
            $tourPackage->fresh()
        );
    }

    /**
     * Display a single tour package.
     */
    public function show(
        TourPackage $tourPackage
    ): TourPackageResource {

        $tourPackage->load('category');

        return new TourPackageResource($tourPackage);
    }

    /**
     * Update a tour package.
     */
    public function update(
        TourPackageRequest $request,
        TourPackage $tourPackage
    ): TourPackageResource {

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Hero Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('hero_image')) {

            if ($tourPackage->hero_image) {
                Storage::disk('public')->delete(
                    $tourPackage->hero_image
                );
            }

            $data['hero_image'] = $request
                ->file('hero_image')
                ->store('tour-package/hero', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Package Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('package_image')) {

            if ($tourPackage->package_image) {
                Storage::disk('public')->delete(
                    $tourPackage->package_image
                );
            }

            $data['package_image'] = $request
                ->file('package_image')
                ->store('tour-package/package', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Package Map Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('package_map_image')) {

            if ($tourPackage->package_map_image) {
                Storage::disk('public')->delete(
                    $tourPackage->package_map_image
                );
            }

            $data['package_map_image'] = $request
                ->file('package_map_image')
                ->store('tour-package/map', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        |
        | If package_name changes, the model automatically
        | regenerates the slug.
        |
        */
        $tourPackage->update($data);

        return new TourPackageResource(
            $tourPackage->fresh()
        );
    }

    /**
     * Delete a tour package.
     */
    public function destroy(
        TourPackage $tourPackage
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Delete Hero Image
        |--------------------------------------------------------------------------
        */
        if ($tourPackage->hero_image) {
            Storage::disk('public')->delete(
                $tourPackage->hero_image
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Package Image
        |--------------------------------------------------------------------------
        */
        if ($tourPackage->package_image) {
            Storage::disk('public')->delete(
                $tourPackage->package_image
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Map Image
        |--------------------------------------------------------------------------
        */
        if ($tourPackage->package_map_image) {
            Storage::disk('public')->delete(
                $tourPackage->package_map_image
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Database Record
        |--------------------------------------------------------------------------
        */
        $tourPackage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tour package deleted successfully.',
        ]);
    }

    public function byCategory(string $category): JsonResponse
{
    $categoryModel = Category::query()
        ->where('slug', $category)
        ->where('is_active', true)
        ->firstOrFail();

    $image = $categoryModel->image;

    if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
        $image = Storage::disk('public')->url($image);
    }

    $packages = $categoryModel
        ->tourPackages()
        ->where('is_active', true)
        ->orderBy('order', 'asc')
        ->get();

    return response()->json([
        'status' => true,

        'category' => [
            'id' => $categoryModel->id,
            'slug' => $categoryModel->slug,
            'image' => $image,
            'country_name' => $categoryModel->country_name,
        ],

        'data' => TourPackageResource::collection($packages),
    ]);
}
}

