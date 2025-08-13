<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        return Item::orderBy('created_at','desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $data['image'] = $path;
        }

        $item = Item::create($data);
        return response()->json($item, 201);
    }

    public function show(Item $item)
    {
        return $item;
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('items', 'public');
            $data['image'] = $path;
        }

        $item->update($data);
        return response()->json($item);
    }

    public function destroy(Item $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();
        return response()->json(['message' => 'Item deleted']);
    }
}
