<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NavItemRequest;
use App\Http\Resources\NavItemResource;
use App\Models\NavItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NavItemController extends Controller
{
    /**
     * Public navigation menu
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        app()->setLocale(
            $request->header('X-Locale', $request->query('lang', 'en'))
        );

        $items = NavItem::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'childrenRecursive' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('order');
                }
            ])
            ->orderBy('order')
            ->get();

        return NavItemResource::collection($items);
    }

    /**
     * Admin navigation menu
     *
     * Includes active + inactive items.
     */
    public function adminIndex(): AnonymousResourceCollection
    {
        $items = NavItem::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('order')
            ->get();

        return NavItemResource::collection($items);
    }

    /**
     * Create navigation item
     */
    public function store(NavItemRequest $request)
    {
        $navItem = NavItem::create(
            $request->validated()
        );

        return (new NavItemResource($navItem))
            ->additional([
                'status' => true,
                'message' => 'Navigation item created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show navigation item
     */
    public function show(NavItem $navItem)
    {
        $navItem->load('childrenRecursive');

        return new NavItemResource($navItem);
    }

    /**
     * Update navigation item
     */
    public function update(
        NavItemRequest $request,
        NavItem $navItem
    ) {
        $data = $request->validated();

        // Prevent itself as parent
        if (
            isset($data['parent_id']) &&
            (int) $data['parent_id'] === $navItem->id
        ) {
            return response()->json([
                'status' => false,
                'message' => 'An item cannot be its own parent.',
            ], 422);
        }

        $navItem->update($data);

        $navItem->load('childrenRecursive');

        return (new NavItemResource($navItem))
            ->additional([
                'status' => true,
                'message' => 'Navigation item updated successfully.',
            ]);
    }

    /**
     * Delete navigation item
     */
    public function destroy(NavItem $navItem)
    {
        if ($navItem->children()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete this item because it has child items.',
            ], 422);
        }

        $navItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'Navigation item deleted successfully.',
        ]);
    }

    /**
     * Reorder navigation items
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:nav_items,id'],
            'items.*.order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['items'] as $item) {
            NavItem::whereKey($item['id'])
                ->update([
                    'order' => $item['order'],
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order updated successfully.',
        ]);
    }
}