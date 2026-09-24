<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HeroSlideRequest;
use App\Http\Resources\HeroSlideResource;
use App\Models\HeroSlide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    // GET /api/hero-slides (public, active only)
    public function index(): AnonymousResourceCollection
    {
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return HeroSlideResource::collection($slides);
    }

    // GET /api/admin/hero-slides (admin, all slides including inactive)
    public function adminIndex(): AnonymousResourceCollection
    {
        $slides = HeroSlide::orderBy('order', 'asc')->get();

        return HeroSlideResource::collection($slides);
    }

    // GET /api/admin/hero-slides/{heroSlide}
    public function show(HeroSlide $heroSlide): HeroSlideResource
    {
        return new HeroSlideResource($heroSlide);
    }

    // POST /api/admin/hero-slides
    public function store(HeroSlideRequest $request): HeroSlideResource
    {
        $data = $request->validated();

        $data['image'] = $request->file('image')->store('hero-slides', 'public');

        $slide = HeroSlide::create($data);

        return new HeroSlideResource($slide);
    }

    // PUT/PATCH /api/admin/hero-slides/{heroSlide}
    public function update(HeroSlideRequest $request, HeroSlide $heroSlide): HeroSlideResource
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Remove the old file before saving the new one
            if ($heroSlide->image) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            $data['image'] = $request->file('image')->store('hero-slides', 'public');
        } else {
            // No new file sent — keep the existing image untouched
            unset($data['image']);
        }

        $heroSlide->update($data);

        return new HeroSlideResource($heroSlide);
    }

    // DELETE /api/admin/hero-slides/{heroSlide}
    public function destroy(HeroSlide $heroSlide): JsonResponse
    {
        if ($heroSlide->image) {
            Storage::disk('public')->delete($heroSlide->image);
        }

        $heroSlide->delete();

        return response()->json(['message' => 'Hero slide deleted successfully.']);
    }
}