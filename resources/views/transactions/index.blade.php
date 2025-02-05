@extends('layouts.app')

@section('content')
@hasrole('admin')
<div class="container mt-4">
    <h1>Daftar Transaksi</h1>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Transaksi</button>
@endhasrole
    <!-- Modal Add Item -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Transaksi Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Pelanggan</label>
                            <select class="form-control" id="user_id" name="user_id">
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" class="form-control" id="total" name="total">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="proofs" class="form-label">Bukti Transaksi</label>
                            <input type="file" class="form-control" id="proofs" name="proofs">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending">Pending</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Tambah Transaksi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tampilkan Daftar Transaksi dalam Card -->
    <div class="row">
        @foreach($transactions as $transaction)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <p class="card-text"><strong>Harga:</strong> Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                    <p class="card-text"><strong>Customer:</strong> {{ $transaction->users->name }}</p>
                    <p class="card-text"><strong>Deskripsi:</strong> {{ $transaction->description }}</p>
                    <p class="card-text"><strong>Bukti:</strong> <a href="{{ asset('storage/' . $transaction->proofs) }}" target="_blank">Lihat Bukti</a></p>
                    <p class="card-text">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $transaction->status == 'pending' ? 'warning text-dark' : ($transaction->status == 'success' ? 'success' : 'danger') }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </p>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#showModal{{ $transaction->id }}">Lihat Detail</button>
                </div>
            </div>
        </div>

        <!-- Modal Show Transaction -->
        <div class="modal fade" id="showModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showModalLabel">Detail Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex">
                            <div class="me-3">
                                <img src="{{ asset('storage/' . $transaction->proofs) }}" alt="Bukti Pembayaran" class="img-fluid" style="max-width: 250px;">
                            </div>
                            <div>
                                <p><strong>Harga:</strong> Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                <p><strong>Customer:</strong> {{ $transaction->users->name }}</p>
                                <p><strong>Deskripsi:</strong> {{ $transaction->description }}</p>
                                <p>
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $transaction->status == 'pending' ? 'warning text-dark' : ($transaction->status == 'success' ? 'success' : 'danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection