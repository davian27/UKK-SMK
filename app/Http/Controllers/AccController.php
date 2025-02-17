<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Item;

class AccController extends Controller
{
    public function acc(Transaction $transaction)
    {
        // Pastikan transaksi berstatus pending sebelum diubah
        if ($transaction->status == 'pending') {
            $transaction->status = 'success';
            $transaction->save();
        }
    
        return redirect()->route('transactions.index')->with('success', 'Transaksi diterima dan status diubah menjadi Success.');
    }

    public function reject(Transaction $transaction)
    {
        // Kembalikan stok barang ke nilai semula jika transaksi ditolak
        foreach ($transaction->items as $item) {
            $stockItem = Item::find($item->id);
            if ($stockItem) {
                $quantity = $item->pivot->quantity;
                $stockItem->increment('stock', $quantity);
            }
        }
    
        $transaction->update([
            'status' => 'failed',
        ]);
    
        return redirect()->route('transactions.index')->with('success', 'Transaksi ditolak dan stok dikembalikan.');
    }
}