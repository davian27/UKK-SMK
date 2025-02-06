@extends('layouts.app')

@section('content')
<div class="container"> <!-- Ubah dari container-fluid ke container -->
    <h1 class="h3 mb-4 text-gray-800">Daftar Transaksi</h1>

    <!-- Tombol Tambah Transaksi -->
    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addTransactionModal">Tambah Transaksi</button>

    <!-- Tabel Transaksi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="margin: 0 auto;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengguna</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Tanggal Transaksi</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaction->users->name }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('j M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $transaction->status == 'pending' ? 'warning text-dark' : ($transaction->status == 'success' ? 'success' : 'danger') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#proofModal{{ $transaction->id }}">
                                    Lihat Bukti
                                </button>
                            </td>
                            <td>
                                @if($transaction->status == 'pending')
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#accTransactionModal{{ $transaction->id }}">Terima</button>
                                @endif
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTransactionModal{{ $transaction->id }}">Edit</button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTransactionModal{{ $transaction->id }}">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada transaksi tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aksi Transaksi (Terima, Edit, Hapus) -->
@foreach ($transactions as $transaction)
    <div class="modal fade" id="accTransactionModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="accTransactionModalLabel{{ $transaction->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.acc', $transaction->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accTransactionModalLabel{{ $transaction->id }}">Terima Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menerima transaksi ini dan mengubah status menjadi <strong>Success</strong>?</p>
                        <p><strong>{{ $transaction->description }}</strong></p>
                        <p><strong>Total: </strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Terima</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach


<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransactionModalLabel">Tambah Transaksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">Pilih Pengguna</label>
                        <select name="user_id" id="user_id" class="form-control">
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi Transaksi</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="proofs">Bukti Transaksi</label>
                        <input type="file" name="proofs" id="proofs" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="total">Total Transaksi</label>
                        <input type="number" name="total" id="total" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status Transaksi</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Transaksi -->
@foreach ($transactions as $transaction)
<div class="modal fade" id="editTransactionModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="editTransactionModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTransactionModalLabel{{ $transaction->id }}">Edit Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">Pilih Pengguna</label>
                        <select name="user_id" id="user_id" class="form-control">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $transaction->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi Transaksi</label>
                        <textarea name="description" id="description" class="form-control">{{ $transaction->description }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="proofs">Bukti Transaksi</label>
                        <input type="file" name="proofs" id="proofs{{ $transaction->id }}" class="form-control" onchange="previewImage({{ $transaction->id }})">
                        <div class="mt-2">
                            <!-- Preview Image -->
                            <img id="preview{{ $transaction->id }}" src="{{ $transaction->proofs ? Storage::url($transaction->proofs) : '' }}" alt="Preview" class="img-fluid" style="max-height: 200px; width: auto;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total">Total Transaksi</label>
                        <input type="number" name="total" id="total" class="form-control" value="{{ $transaction->total }}" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status Transaksi</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="success" {{ $transaction->status == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ $transaction->status == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update Transaksi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Hapus Transaksi -->
@foreach ($transactions as $transaction)
<div class="modal fade" id="deleteTransactionModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="deleteTransactionModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTransactionModalLabel{{ $transaction->id }}">Hapus Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
                    <p><strong>{{ $transaction->description }}</strong></p>
                    <p><strong>Total: </strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Lihat Bukti Transaksi -->
@foreach ($transactions as $transaction)
<div class="modal fade" id="proofModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="proofModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofModalLabel{{ $transaction->id }}">Bukti Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Foto di sebelah kiri -->
                    <div class="col-md-6">
                        <strong>Bukti Transaksi:</strong>
                        <br>
                        @if($transaction->proofs)
                            <a href="{{ Storage::url($transaction->proofs) }}" target="_blank">
                                <img src="{{ Storage::url($transaction->proofs) }}" alt="Bukti Transaksi" class="img-fluid" style="max-height: 300px; width: auto;">
                            </a>
                        @else
                            <p>Tidak ada bukti yang diunggah.</p>
                        @endif
                    </div>

                    <!-- Deskripsi di sebelah kanan -->
                    <div class="col-md-6 mt-4">
                        <strong>Pengguna:</strong>
                        <p>{{ $transaction->users->name }}</p>

                        <strong>Deskripsi:</strong>
                        <p>{{ $transaction->description }}</p>

                        <strong>Total:</strong>
                        <p>Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>

                        <strong>Status:</strong>
                        <p>
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

<script>
    function previewImage(transactionId) {
        var file = document.getElementById('proofs' + transactionId).files[0];
        var reader = new FileReader();

        reader.onloadend = function() {
            document.getElementById('preview' + transactionId).src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            document.getElementById('preview' + transactionId).src = '';
        }
    }
</script>
@endsection
