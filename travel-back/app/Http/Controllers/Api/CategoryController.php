<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->orderBy('order', 'asc')
            ->get();

        return CategoryResource::collection($categories);
    }


    public function store(CategoryRequest $request): CategoryResource
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('category', 'public');
        }

        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        $category = Category::create($data);

        return new CategoryResource($category);
    }


    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function update( CategoryRequest $request, Category $category): CategoryResource 
    {

        $data = $request->validated();

        // Upload new image
        if ($request->hasFile('image')) {

            if ($category->image) {
                Storage::disk('public')->delete( $category->image );
            }

            $data['image'] = $request ->file('image') ->store('category', 'public');
        }

        $category->update($data);

        return new CategoryResource(
            $category->fresh()
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->image) {
            Storage::disk('public')->delete(
                $category->image
            );
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }


}