<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestItem;
use App\Models\Item;

class RequestItemController extends Controller
{
    public function index()
    {
        return RequestItem::with('item')->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'borrower_id' => 'required|string|max:255',
            'date' => 'required|date',
            'time_in' => 'required',
            'time_out' => 'required',
            'item_id' => 'required|exists:items,id',
        ]);

        $requestItem = RequestItem::create($data);
        return response()->json($requestItem->load('item'), 201);
    }

    public function show(RequestItem $requestItem)
    {
        return $requestItem->load('item');
    }

    public function update(Request $request, RequestItem $requestItem)
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);
        $requestItem->update($data);
        return response()->json($requestItem);
    }

    public function destroy(RequestItem $requestItem)
    {
        $requestItem->delete();
        return response()->json(['message' => 'Request deleted']);
    }
}