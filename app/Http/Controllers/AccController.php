<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

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
}
