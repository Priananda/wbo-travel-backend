<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaketTour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaketTourController extends Controller
{
    public function index()
    {
        return response()->json(PaketTour::where('active', true)->get());
    }

    public function show($id)
    {
        $paket = PaketTour::findOrFail($id);
        return response()->json($paket);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'location' => 'nullable|string',
            'image' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $paket = PaketTour::create($validated);

        return response()->json($paket, 201);
    }

    public function update(Request $request, $id)
    {
        $paket = PaketTour::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'location' => 'nullable|string',
            'image' => 'nullable|string',
            'active' => 'boolean',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $paket->update($validated);

        return response()->json($paket);
    }

    public function destroy($id)
    {
        $paket = PaketTour::findOrFail($id);
        $paket->delete();

        return response()->json(['message' => 'Paket deleted successfully']);
    }
}
