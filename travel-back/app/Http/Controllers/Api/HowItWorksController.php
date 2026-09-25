<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HowItWorksStepRequest;
use App\Http\Resources\HowItWorksStepResource;
use App\Models\HowItWorksStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HowItWorksController extends Controller
{
 
   public function index(): AnonymousResourceCollection
    {
        $steps = HowItWorksStep::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return HowItWorksStepResource::collection($steps);
    }

    /**
     * Display all steps (active & inactive) for the admin area.
     */
    public function adminIndex(): AnonymousResourceCollection
    {
        $steps = HowItWorksStep::query()
            ->orderBy('order', 'asc')
            ->get();

        return HowItWorksStepResource::collection($steps);
    }

    /**
     * Store a newly created step.
     */
    public function store(HowItWorksStepRequest $request): JsonResponse
    {
        $step = HowItWorksStep::create($request->validated());

        return response()->json([
            'message' => 'Step created successfully.',
            'data'    => new HowItWorksStepResource($step),
        ], 210);
    }

    /**
     * Display the specified step.
     */
    public function show(HowItWorksStep $howItWorksStep): HowItWorksStepResource
    {
        return new HowItWorksStepResource($howItWorksStep);
    }

    /**
     * Update the specified step.
     */
    public function update(HowItWorksStepRequest $request, HowItWorksStep $howItWorksStep): JsonResponse
    {
        $howItWorksStep->update($request->validated());

        return response()->json([
            'message' => 'Step updated successfully.',
            'data'    => new HowItWorksStepResource($howItWorksStep),
        ]);
    }

    /**
     * Remove the specified step from storage.
     */
    public function destroy(HowItWorksStep $howItWorksStep): JsonResponse
    {
        $howItWorksStep->delete();

        return response()->json([
            'message' => 'Step deleted successfully.',
        ]);
    }
}


