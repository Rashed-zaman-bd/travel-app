<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HowItWorksStepRequest;
use App\Http\Resources\HowItWorksStepResource;
use App\Models\HowItWorksStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HowItWorksStepController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $steps = HowItWorksStep::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return HowItWorksStepResource::collection($steps);
    }

    public function adminIndex(): AnonymousResourceCollection
    {
        $steps = HowItWorksStep::orderBy('order', 'asc')->get();

        return HowItWorksStepResource::collection($steps);
    }

    public function show(HowItWorksStep $howItWorksStep): HowItWorksStepResource
    {
        return new HowItWorksStepResource($howItWorksStep);
    }

    public function store(HowItWorksStepRequest $request): HowItWorksStepResource
    {
        $step = HowItWorksStep::create($request->validated());

        return new HowItWorksStepResource($step);
    }

    public function update(HowItWorksStepRequest $request, HowItWorksStep $howItWorksStep): HowItWorksStepResource
    {
        $howItWorksStep->update($request->validated());

        return new HowItWorksStepResource($howItWorksStep);
    }

    public function destroy(HowItWorksStep $howItWorksStep): JsonResponse
    {
        $howItWorksStep->delete();

        return response()->json(['message' => 'Step deleted successfully.']);
    }
}
