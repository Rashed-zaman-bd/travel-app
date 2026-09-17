<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NavItemRequest;
use App\Http\Resources\NavItemResource;
use App\Models\NavItem;
use Illuminate\Http\Request;

class NavItemController extends Controller
{
    // Public: used by the site header
    public function index()
    {
        $navItems = NavItem::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('order')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Navigation items retrieved successfully.',
            'data'    => NavItemResource::collection($navItems),
        ]);
    }

    // Admin: full tree, including inactive items — THIS WAS MISSING
    public function adminIndex()
    {
        $navItems = NavItem::whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => NavItemResource::collection($navItems),
        ]);
    }

    public function store(NavItemRequest $request)
    {
        $navItem = NavItem::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Navigation item created successfully.',
            'data'    => new NavItemResource($navItem),
        ], 201);
    }

    public function show(NavItem $navItem)
    {
        return response()->json([
            'status' => true,
            'data'   => new NavItemResource($navItem->load('children')),
        ]);
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

        return response()->json([
            'status'  => true,
            'message' => 'Navigation item updated successfully.',
            'data'    => new NavItemResource($navItem->fresh()),
        ]);
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