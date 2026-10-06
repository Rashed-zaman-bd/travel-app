<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferShowRequest;
use App\Http\Resources\OfferShowResource;
use App\Models\OfferShow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class OfferShowController extends Controller
{
     public function index(): AnonymousResourceCollection
    {
        $offerShow = OfferShow::query()
            ->orderBy('order', 'asc')
            ->get();

        return OfferShowResource::collection($offerShow);
    }


    public function show( OfferShow $offerShow ): OfferShowResource 
    {

        return new OfferShowResource($offerShow);
    }


    public function store( OfferShowRequest $request ): OfferShowResource 
    {

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('offer-show/image', 'public');
        }

        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;


        $offerShow = OfferShow::create($data);

        return new OfferShowResource(
            $offerShow->fresh()
        );
    }


    public function update( OfferShowRequest $request, OfferShow $offerShow ): OfferShowResource 
    {

        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($offerShow->image) {
                Storage::disk('public')->delete(
                    $offerShow->image
                );
            }

            $data['image'] = $request
                ->file('image')
                ->store('offer-show/image', 'public');
        }

        $offerShow->update($data);

        return new OfferShowResource(
            $offerShow->fresh()
        );
    }


     public function destroy( OfferShow $offerShow ): JsonResponse 
    {

        if ($offerShow->image) {
            Storage::disk('public')->delete(
                $offerShow->image
            );
        }

        $offerShow->delete();

        return response()->json([
            'status' => true,
            'message' => 'Offer show deleted successfully.',
        ]);
    }
}
