<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NavItemRequest;
use App\Http\Resources\NavItemResource;
use App\Models\NavItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection; // Fixed import

class NavItemController extends Controller
{
    // Public: Site header menu
    public function index(): AnonymousResourceCollection
    {
        $items = NavItem::query()
            ->whereNull('parent_id') 
            ->where('is_active', true)
            ->with(['childrenRecursive' => function ($query) {
                // Cascades active filter down through all nested levels
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return NavItemResource::collection($items);
    }

    // Admin: Full tree including inactive items
    public function adminIndex(): AnonymousResourceCollection
    {
        $items = NavItem::query()
            ->whereNull('parent_id') 
            ->where('is_active', true)
            ->with(['childrenRecursive' => function ($query) {
                // Cascades active filter down through all nested levels
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return NavItemResource::collection($items);
    }

    public function store(NavItemRequest $request)
    {
        $navItem = NavItem::create($request->validated());

        return (new NavItemResource($navItem))
            ->additional(['status' => true, 'message' => 'Navigation item created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(NavItem $navItem)
    {
        return new NavItemResource($navItem->load('childrenRecursive'));
    }

    public function update(NavItemRequest $request, NavItem $navItem)
    {
        $data = $request->validated();

        if (isset($data['parent_id']) && (int) $data['parent_id'] === $navItem->id) {
            return response()->json([
                'status'  => false,
                'message' => 'An item cannot be its own parent.',
            ], 422);
        }

        $navItem->update($data);

        return (new NavItemResource($navItem->fresh()))
            ->additional(['status' => true, 'message' => 'Navigation item updated successfully.']);
    }

    public function destroy(NavItem $navItem)
    {
        $navItem->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Navigation item deleted successfully.',
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'         => ['required', 'array'],
            'items.*.id'    => ['required', 'exists:nav_items,id'],
            'items.*.order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->items as $item) {
            NavItem::whereKey($item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['status' => true, 'message' => 'Order updated.']);
    }
}