<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $transactions = Transaction::whereHas('users', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%");
        })->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
        ->orderBy('id', 'desc')->get();
        $items = Item::all();
        $users = User::role('customer')->get();
        return view('transactions.index', compact('transactions', 'users','items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
{
    $total = 0;
    $itemsToAttach = [];

    foreach ($request->items as $index => $item_id) {
        $item = Item::find($item_id);
        $quantity = $request->quantities[$index] ?? 1;

        // Cek apakah stok cukup sebelum menyimpan gambar
        if (!$item || $item->stock < $quantity) {
            return redirect()->back()->with('error', "Stok untuk {$item->name} tidak mencukupi! Stok tersedia: {$item->stock}");
        }

        // Tambahkan item ke transaksi (jika stok cukup)
        $itemsToAttach[$item_id] = ['quantity' => $quantity];
        $total += $item->price * $quantity;
    }

    // Jika semua item valid, baru proses penyimpanan file
    if ($request->hasFile('proofs')) {
        $photo = $request->file('proofs');
        $path = $photo->store('proofs', 'public');
    } else {
        $path = null;
    }

    // Buat transaksi baru setelah stok diverifikasi
    $transaction = Transaction::create([
        'user_id' => $request->user_id,
        'total' => $total,
        'proofs' => $path,
        'description' => $request->description,
        'transaction_date' => $request->transaction_date ?? now(),
        'status' => $request->status,
    ]);

    // Simpan item yang telah diverifikasi ke transaksi
    $transaction->items()->attach($itemsToAttach);

    // Kurangi stok setelah transaksi berhasil dibuat
    foreach ($request->items as $index => $item_id) {
        $item = Item::find($item_id);
        $quantity = $request->quantities[$index] ?? 1;
        $item->stock -= $quantity;
        $item->save();
    }

    return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
}


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
{
    // Kembalikan stok barang ke nilai semula sebelum mengupdate transaksi
    foreach ($transaction->items as $olditem) {
        $item = Item::find($olditem->id);
        if ($item) { // Pastikan item tidak null
            $oldquantity = $olditem->pivot->quantity;
            $item->increment('stock', $oldquantity);
        }
    }

    // Hapus hubungan dengan item lama
    $transaction->items()->detach();

    // Validasi stok sebelum mengupdate transaksi
    foreach ($request->items as $index => $item_id) {
        $item = Item::find($item_id);
        if ($item) {
            $quantity = $request->quantities[$index] ?? 1;
            if ($item->stock < $quantity) {
                return redirect()->back()->withErrors(["items.$index" => "Stok tidak mencukupi untuk item $item->name"]);
            }
        }
    }

    // Simpan bukti transaksi jika ada file yang diunggah
    if ($request->hasFile('proof')) {
        if ($transaction->proof) {
            Storage::disk('public')->delete($transaction->proof);
        }
        $path = $request->file('proof')->store('proofs', 'public');
        $transaction->proof = $path;
    }

    // Update transaksi utama
    $transaction->update([
        'user_id' => $request->user_id,
        'description' => $request->description,
        'transaction_date' => $request->transaction_date,
        'status' => $request->status ?? 'pending',
    ]);

    // Update item baru dan kurangi stok
    $total = 0;
    $itemsData = [];

    foreach ($request->items as $index => $item_id) {
        $item = Item::find($item_id);
        if ($item) {
            $quantity = $request->quantities[$index] ?? 1;
            $itemsData[$item_id] = ['quantity' => $quantity];
            $item->decrement('stock', $quantity);
            $total += $item->price * $quantity;
        }
    }

    // Simpan ulang item ke pivot table menggunakan sync()
    $transaction->items()->sync($itemsData);

    // Update total harga transaksi
    $transaction->update(['total' => $total]);

    return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->items()->detach();
            if ($transaction->proofs) {
                Storage::disk('public')->delete($transaction->proofs);
            }

            $transaction->delete();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Failed to delete transaction. ');
        }
    }


}