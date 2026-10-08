<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageReviewRequest;
use App\Http\Resources\TourPackageReviewResource;
use App\Models\TourPackageReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class TourPackageReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // only admins may see unapproved reviews
        $showAll = $request->boolean('include_unapproved') && $this->isAdmin($request->user('sanctum'));

        $reviews = TourPackageReview::query()
            ->with('user')
            ->when(!$showAll, fn ($q) => $q->approved())
            ->when($request->filled('tour_package_id'), fn ($q) => $q->where('tour_package_id', $request->integer('tour_package_id')))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->integer('rating')))
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return TourPackageReviewResource::collection($reviews);
    }

    /**
     * One review per user per package: a second submit updates the first.
     */
    public function store(TourPackageReviewRequest $request): JsonResponse
    {
        $data = $request->validated();

        $review = TourPackageReview::updateOrCreate(
            [
                'tour_package_id' => $data['tour_package_id'],
                'user_id'         => $request->user()->id,
            ],
            [
                ...Arr::except($data, ['tour_package_id']),
                'is_approved' => true, // set false here if you want admin moderation
            ]
        );

        $review->load('user');

        return response()->json([
            'message' => $review->wasRecentlyCreated
                ? 'Review created successfully.'
                : 'Your review has been updated.',
            'data'    => new TourPackageReviewResource($review),
        ], $review->wasRecentlyCreated ? 201 : 200);
    }

    public function show(TourPackageReview $tourPackageReview): TourPackageReviewResource
    {
        abort_unless($tourPackageReview->is_approved, 404);

        return new TourPackageReviewResource($tourPackageReview->load('user'));
    }

    public function update(TourPackageReviewRequest $request, TourPackageReview $tourPackageReview): JsonResponse
    {
        if (!$this->canManage($request, $tourPackageReview)) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // the package of a review can't be changed
        $tourPackageReview->update(Arr::except($request->validated(), ['tour_package_id']));

        return response()->json([
            'message' => 'Review updated successfully.',
            'data'    => new TourPackageReviewResource($tourPackageReview->load('user')),
        ]);
    }

    public function destroy(Request $request, TourPackageReview $tourPackageReview): JsonResponse
    {
        if (!$this->canManage($request, $tourPackageReview)) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $tourPackageReview->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    /**
     * Admin only (see routes).
     */
    public function toggleApproval(TourPackageReview $tourPackageReview): JsonResponse
    {
        $tourPackageReview->update(['is_approved' => !$tourPackageReview->is_approved]);

        return response()->json([
            'message'     => 'Review approval status updated.',
            'is_approved' => $tourPackageReview->is_approved,
        ]);
    }

    private function isAdmin($user): bool
    {
        return $user && in_array($user->role, ['admin', 'super_admin'], true);
    }

    private function canManage(Request $request, TourPackageReview $review): bool
    {
        $user = $request->user();

        return $user->id === $review->user_id || $this->isAdmin($user);
    }
}