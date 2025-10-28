<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaketTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaketTourController extends Controller
{
    // public function index()
    // {
    //     return response()->json(PaketTour::where('active', true)->get());
    // }


    // public function show($id)
    // {
    //     $paket = PaketTour::findOrFail($id);
    //     return response()->json($paket);
    // }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 8);
        $sort = $request->get('sort', 'default');

        $query = PaketTour::where('active', true);

        // Sorting
        switch ($sort) {
            case 'popularity':
                $query->orderBy('views', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $paketTours = $query->paginate($perPage);

        return response()->json([
            'data' => $paketTours->items(),
            'meta' => [
                'current_page' => $paketTours->currentPage(),
                'last_page' => $paketTours->lastPage(),
                'total' => $paketTours->total(),
            ]
        ]);
    }

    public function show($slug)
    {
        $paket = PaketTour::where('slug', $slug)->firstOrFail();
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'active' => 'boolean',
            'duration_days' => 'nullable|integer|min:1',
            'duration_nights' => 'nullable|integer|min:0',
            'feature_duration_days' => 'nullable|integer|min:1',
            'minimum_age' => 'nullable|integer|min:0',
            'pickup_location' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Simpan gambar jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('paket_tours', 'public');
            $validated['image'] = $path; // simpan path ke database
        }

        $paket = PaketTour::create($validated);

        return response()->json([
            'message' => 'Paket Tour berhasil dibuat!',
            'data' => $paket
        ], 201);
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'active' => 'boolean',
            'duration_days' => 'nullable|integer|min:1',
            'duration_nights' => 'nullable|integer|min:0',
            'feature_duration_days' => 'nullable|integer|min:1',
            'minimum_age' => 'nullable|integer|min:0',
            'pickup_location' => 'nullable|string|max:255',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // 🖼️ Jika ada gambar baru, hapus yang lama lalu upload baru
        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($paket->image && Storage::disk('public')->exists($paket->image)) {
                Storage::disk('public')->delete($paket->image);
            }

            $path = $request->file('image')->store('paket_tours', 'public');
            $validated['image'] = $path;
        }

        $paket->update($validated);

        return response()->json([
            'message' => 'Paket Tour berhasil diperbarui!',
            'data' => $paket
        ]);
    }

    public function destroy($id)
    {
        $paket = PaketTour::findOrFail($id);

        // 🧹 Hapus gambar dari storage kalau ada
        if ($paket->image && Storage::disk('public')->exists($paket->image)) {
            Storage::disk('public')->delete($paket->image);
        }

        $paket->delete();

        return response()->json(['message' => 'Paket Tour berhasil dihapus!']);
    }
}
