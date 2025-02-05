<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::orderBy('id', 'desc')->get();
        $users = User::role('customer')->get();
        // dd($users->all());
        return view('transactions.index', compact('transactions', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $photo = $request->file('proofs');
        $path = $photo->store('proofs', 'public');

        Transaction::create([
            'user_id' => $request->user_id,
            'total' => $request->total,
            'proofs' => $path,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $data = $request->all();

        if ($request->hasFile('proofs')) {
            if ($transaction->proofs) {
                Storage::disk('public')->delete($transaction->proofs);
            }
            $data['proofs'] = $request->file('proofs')->store('proofs', 'public');
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            if ($transaction->proofs) {
                Storage::disk('public')->delete($transaction->proofs);
            }

            $transaction->delete();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Failed to delete transaction. ' . $e->getMessage());
        }
    }
}