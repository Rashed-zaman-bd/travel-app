<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopBannerRequest;
use App\Http\Requests\UpdateTopBannerRequest;
use App\Models\TopBanner;
use Illuminate\Support\Facades\Storage;

class TopBannerController extends Controller
{
    // Public: only active banners, in display order
    public function index()
    {
        $banners = TopBanner::active()->get();

        return response()->json([
            'status' => true,
            'message' => 'Top banners fetched successfully',
            'data' => $banners,
        ]);
    }

    // Admin: every banner, active or not, in display order
    public function adminIndex()
    {
        $banners = TopBanner::orderBy('order')->get();

        return response()->json([
            'status' => true,
            'message' => 'Top banners fetched successfully',
            'data' => $banners,
        ]);
    }

    public function store(StoreTopBannerRequest $request)
    {
        $validated = $request->validated();
        $validated['image'] = $request->file('image')->store('top-banners', 'public');

        $banner = TopBanner::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Banner created successfully',
            'data' => $banner,
        ], 201);
    }

    public function show(TopBanner $topBanner)
    {
        return response()->json([
            'status' => true,
            'message' => 'Banner fetched successfully',
            'data' => $topBanner,
        ]);
    }

    public function update(UpdateTopBannerRequest $request, TopBanner $topBanner)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($topBanner->image) {
                Storage::disk('public')->delete($topBanner->getRawOriginal('image'));
            }
            $validated['image'] = $request->file('image')->store('top-banners', 'public');
        }

        $topBanner->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Banner updated successfully',
            'data' => $topBanner,
        ]);
    }

    public function destroy(TopBanner $topBanner)
    {
        if ($topBanner->image) {
            Storage::disk('public')->delete($topBanner->getRawOriginal('image'));
        }

        $topBanner->delete();

        return response()->json([
            'status' => true,
            'message' => 'Banner deleted successfully',
        ]);
    }
}