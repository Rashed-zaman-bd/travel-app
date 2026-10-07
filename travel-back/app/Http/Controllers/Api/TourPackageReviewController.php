<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourPackageReviewRequest;
use App\Http\Resources\TourPackageReviewResource;
use App\Models\TourPackage;
use App\Models\TourPackageReview;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourPackageReviewController extends Controller
{
    private const WITH = [
        'user:id,name,avatar',
        'tourPackage:id,slug,package_name,location',
    ];

    /**
     * Public: approved reviews of one package + rating summary.
     */
    public function index(TourPackage $tourPackage): JsonResponse
    {
        $base = $tourPackage->reviews()->approved();

        $reviews = (clone $base)
            ->with(self::WITH)
            ->latest()
            ->paginate(10);

        // [5 => 12, 4 => 3, ...]
        $stats   = (clone $base)->selectRaw('rating, COUNT(*) as total')->groupBy('rating')->pluck('total', 'rating');
        $count   = (int) $stats->sum();
        $average = $count
            ? round($stats->reduce(fn ($carry, $total, $rating) => $carry + ($rating * $total), 0) / $count, 1)
            : 0;

        return response()->json([
            'summary' => [
                'average'   => $average,
                'count'     => $count,
                'breakdown' => collect([5, 4, 3, 2, 1])
                    ->mapWithKeys(fn ($star) => [$star => (int) ($stats[$star] ?? 0)]),
            ],
            // keeps the shape { data: [...], links: {...}, meta: {...} } that the Vue component reads
            'data' => TourPackageReviewResource::collection($reviews)->response()->getData(true),
        ]);
    }

    /**
     * Logged-in user: create or update their own review for this package.
     * Name and avatar come from the users table through user_id.
     */
    public function store(TourPackageReviewRequest $request, TourPackage $tourPackage): JsonResponse
    {
        abort_unless($tourPackage->is_active, 404);

        $review = $tourPackage->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $this->prepare($request->validated())
        );

        $review->load(self::WITH);

        return response()->json([
            'status'  => true,
            'message' => $review->is_approved
                ? 'Thanks! Your review has been published.'
                : 'Thanks! Your review will appear after approval.',
            'data'    => new TourPackageReviewResource($review),
        ], $review->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Admin: all reviews. Filters: ?pending=1, ?package_id=5
     */
    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $reviews = TourPackageReview::query()
            ->with(self::WITH)
            ->when($request->boolean('pending'), fn ($q) => $q->where('is_approved', false))
            ->when($request->filled('package_id'), fn ($q) => $q->where('tour_package_id', $request->integer('package_id')))
            ->latest()
            ->paginate(20);

        return TourPackageReviewResource::collection($reviews);
    }

    /**
     * Admin: approve or hide a review.
     */
    public function approve(TourPackageReview $review): JsonResponse
    {
        $review->update(['is_approved' => true]);

        return response()->json(['status' => true, 'message' => 'Review approved.']);
    }

    public function unapprove(TourPackageReview $review): JsonResponse
    {
        $review->update(['is_approved' => false]);

        return response()->json(['status' => true, 'message' => 'Review hidden.']);
    }

    public function destroy(TourPackageReview $review): JsonResponse
    {
        $review->delete();

        return response()->json(['status' => true, 'message' => 'Review deleted.']);
    }

    /**
     * Convert "2026-09" or "Sep-2026" to "2026-09-01" for the `date` column.
     */
    private function prepare(array $data): array
    {
        if (!empty($data['travel_date'])) {
            $format = preg_match('/^\d{4}-\d{2}$/', $data['travel_date']) ? '!Y-m' : '!M-Y';

            $data['travel_date'] = Carbon::createFromFormat($format, $data['travel_date'])->toDateString();
        }

        return $data;
    }
}