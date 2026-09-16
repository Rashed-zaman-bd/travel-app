<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLogoRequest;
use App\Http\Requests\UpdateLogoRequest;
use App\Http\Resources\LogoResource;
use App\Models\Logo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function index()
    {
        $logo = Logo::latest()->first();

        if (! $logo) {
            return response()->json([
                'message' => 'Logo not found.',
            ], 404);
        }

        return new LogoResource($logo);
    }

    public function store(CreateLogoRequest $request): JsonResponse
    {
        // If only one logo is allowed
        $logo = Logo::first();

        if ($logo) {
            return response()->json([
                'message' => 'Logo already exists. Please update the existing logo.',
            ], 422);
        }

        $data = [
            'title' => $request->title,
        ];

        if ($request->hasFile('text_logo')) {
            $data['text_logo'] = $request
                ->file('text_logo')
                ->store('logos', 'public');
        }

        if ($request->hasFile('round_logo')) {
            $data['round_logo'] = $request
                ->file('round_logo')
                ->store('logos', 'public');
        }

        $logo = Logo::create($data);

        return response()->json([
            'message' => 'Logo created successfully.',
            'data' => new LogoResource($logo),
        ], 201);
    }

    public function update(UpdateLogoRequest $request, Logo $logo): JsonResponse
    {
        $data = [
            'title' => $request->title,
        ];

        // Replace text logo
        if ($request->hasFile('text_logo')) {
            if ($logo->getRawOriginal('text_logo')) {
                Storage::disk('public')->delete($logo->getRawOriginal('text_logo'));
            }

            $data['text_logo'] = $request
                ->file('text_logo')
                ->store('logos', 'public');
        }

        // Replace round logo
        if ($request->hasFile('round_logo')) {
            if ($logo->getRawOriginal('round_logo')) {
                Storage::disk('public')->delete($logo->getRawOriginal('round_logo'));
            }

            $data['round_logo'] = $request
                ->file('round_logo')
                ->store('logos', 'public');
        }

        $logo->update($data);

        return response()->json([
            'message' => 'Logo updated successfully.',
            'data' => new LogoResource($logo),
        ]);
    }


    public function destroy(Logo $logo): JsonResponse
    {
        if ($logo->getRawOriginal('text_logo')) {
            Storage::disk('public')->delete($logo->getRawOriginal('text_logo'));
        }

        if ($logo->getRawOriginal('round_logo')) {
            Storage::disk('public')->delete($logo->getRawOriginal('round_logo'));
        }

        $logo->delete();

        return response()->json([
            'message' => 'Logo deleted successfully.',
        ]);
    }
}