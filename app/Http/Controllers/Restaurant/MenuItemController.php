<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;
        $items = MenuItem::where('restaurant_id', $restaurant->id)->latest()->get();

        return view('restaurant.menu', compact('items'));
    }

    public function store(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'menu-items/' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

            $dir = storage_path('app/public/menu-items');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $file->move($dir, basename($filename));
            $data['image_path'] = $filename;
        }

        unset($data['image']);

        $restaurant->menuItems()->create($data);

        return back()->with('success', 'Menu item added.');
    }

    public function update(Request $request, MenuItem $menuItem)
{
    $restaurant = auth()->user()->restaurant;
    abort_unless($menuItem->restaurant_id === $restaurant->id, 403);

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
    ]);

    if ($request->hasFile('image')) {
        // Delete old
        if ($menuItem->image_path) {
            $old = storage_path('app/public/' . $menuItem->image_path);
            if (file_exists($old)) @unlink($old);
        }

        $file = $request->file('image');
        $filename = 'menu-items/' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        $dir = storage_path('app/public/menu-items');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, basename($filename));
        $data['image_path'] = $filename;
    }

    unset($data['image']);
    $menuItem->update($data);

    return back()->with('success', 'Menu item updated.');
}

    public function toggleAvailability(MenuItem $menuItem)
    {
        $restaurant = auth()->user()->restaurant;
        abort_unless($menuItem->restaurant_id === $restaurant->id, 403);

        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return back()->with('success', $menuItem->is_available
            ? 'Item marked as available.'
            : 'Item marked as unavailable.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $restaurant = auth()->user()->restaurant;
        abort_unless($menuItem->restaurant_id === $restaurant->id, 403);

        // Soft delete — hindi na kailangan i-unlink ang image
        // para kung i-restore, buo pa rin ang picture
        $menuItem->delete();

        return back()->with('success', 'Menu item deleted.');
    }
}