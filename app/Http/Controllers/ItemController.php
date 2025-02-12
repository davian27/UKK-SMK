<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $items = Item::orderBy('id', 'desc')
                ->where('name', 'like', "%$search%")
                ->get();
        return view('items.index', compact('items'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $photo = $request->file('photo');
        $path = $photo->store('items', 'public');

        Item::create([
            'photo' => $path,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Menampilkan form edit jika diperlukan
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        
        // Memeriksa apakah ada foto baru yang diunggah
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }

            // Simpan foto baru
            $photo = $request->file('photo');
            $path = $photo->store('items', 'public');
            $item->photo = $path;
        }

        // Memperbarui atribut lainnya
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;

        // Simpan perubahan
        $item->save();

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            // Menghapus foto terkait jika ada
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }

            // Menghapus item dari database
            $item->delete();

            // Mengembalikan response sukses
            return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            // Menangkap pengecualian dan mengembalikan response error
            return redirect()->route('items.index')->with('error', 'Failed to delete item. ');
        }
    }
}
