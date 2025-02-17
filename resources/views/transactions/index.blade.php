@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800">Daftar Transaksi</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Tombol Tambah Transaksi -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
            Tambah Transaksi
        </button>

        <!-- Form Search -->
        <form action="{{ route('transactions.index') }}" method="GET" class="d-flex align-items-center border p-2 rounded">
            <div class="input-group">
                <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Cari transaksi..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>


    <!-- Tabel Transaksi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                            @hasrole('admin')
                            <th>Approval</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaction->users->name }}</td>
                            <td>{{ $transaction->description ?: 'Tidak Ada Deskripsi' }}</td>
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
                                
                                @hasrole('admin')
                                @if($transaction->status == 'pending' || $transaction->status == 'failed')
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTransactionModal{{ $transaction->id }}">
                                    Hapus
                                </button>
                                @endif
                                @endhasrole
                                @if($transaction->status == 'pending')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTransactionModal{{ $transaction->id }}">Edit</button>
                                @endif
                                @if($transaction->status == 'success')
                                <span class="badge bg-success text-light">Pesanan selesai</span>
                                @endif
                                @hasrole('customer')
                                @if($transaction->status == 'failed')
                                <span class="badge bg-danger text-light">Pesanan gagal</span>
                                @endif
                                @endhasrole
                                
                            </td>
                            @hasrole('admin')
                            <td>
                                @if($transaction->status == 'pending')
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#accTransactionModal{{ $transaction->id }}">Terima</button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectTransactionModal{{ $transaction->id }}">Tolak</button>
                                @elseif($transaction->status == 'success')
                                <span class="badge bg-success text-light">Sudah Disetujui</span>
                                @else
                                <span class="badge bg-danger text-light">Ditolak</span>
                                @endif
                            </td>
                            @endhasrole
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

<!-- Modal Tolak Transaksi -->
@foreach ($transactions as $transaction)
<div class="modal fade" id="rejectTransactionModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="rejectTransactionModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transactions.reject', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectTransactionModalLabel{{ $transaction->id }}">Tolak Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak transaksi ini dan mengubah status menjadi <strong>Failed</strong>?</p>
                    <p><strong>{{ $transaction->description }}</strong></p>
                    <p><strong>Total: </strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Show Transaksi -->
@foreach($transactions as $transaction)
<div class="modal fade" id="transactionModalShow{{ $transaction->id }}" tabindex="-1" aria-labelledby="transactionModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel{{ $transaction->id }}">Detail Struk Belanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Using Bootstrap Grid to split the content into two columns -->
                <div class="row">
                    <div class="col-md-6">
                        @if ($transaction->proofs)
                        <div class="mb-3">
                            <h6>Payment Proof:</h6>
                            <img src="{{ asset('storage/' . $transaction->proofs) }}" alt="Payment Proof" class="img-fluid" />
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6 mt-4">
                        <h6>User: {{ $transaction->users->name }}</h6>
                        <p>Status: {{ $transaction->status }}</p>
                        <p>Description: {{ $transaction->description }}</p>
                        <p>Total: Rp. {{ number_format($transaction->total, 2) }}</p>
                        <p>Date: {{ $transaction->created_at->format('Y-m-d H:i') }}</p>

                        <h6>Items Purchased:</h6>
                        <ul>
                            @foreach ($transaction->items as $item)
                            <li>{{ $item->name }} - Quantity: {{ $item->pivot->quantity }} - Price: Rp. {{ number_format($item->price, 2) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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
                    <!-- Bukti Transaksi -->
                    <div class="form-group">
                        <label for="proof">Bukti Transaksi</label>
                        <input type="file" name="proofs" id="proof" class="form-control">
                    </div>

                    <!-- Pilih Pengguna -->
                    <div class="form-group">
                        <label for="user_id">Pengguna</label>
                        <select name="user_id" id="user_id" class="form-control">
                            @foreach($users as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>

                    <!-- Tanggal Transaksi -->
                    <div class="form-group">
                        <label for="transaction_date">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" id="transaction_date" class="form-control">
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" name="status" id="status" class="form-control" value="pending" readonly>
                    </div>

                    <!-- Pilih Item dan Kuantitas -->
                    <div class="form-group">
                        <label for="items">Pilih Item</label>
                        <div id="items-container">
                            <div class="d-flex mb-2">
                                <select name="items[]" class="form-control mr-2">
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} - (Stok: {{ $item->stock }})
                                    </option>
                                    @endforeach

                                </select>
                                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" value="1">
                                <button type="button" class="btn btn-success ml-2 add-item">+</button>
                            </div>
                        </div>
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
                    <!-- Bukti Transaksi -->
                    <div class="form-group">
                        <label for="proofs">Bukti Transaksi</label>
                        <input type="file" name="proofs" id="proofs{{ $transaction->id }}" class="form-control" onchange="previewImage({{ $transaction->id }})">
                        <div class="mt-2">
                            <img id="preview{{ $transaction->id }}" src="{{ $transaction->proofs ? Storage::url($transaction->proofs) : '' }}" alt="Preview" class="img-fluid" style="max-height: 200px; width: auto;">
                        </div>
                    </div>

                    <!-- Pilih Pengguna -->
                    <div class="form-group">
                        <label for="user_id">Pengguna</label>
                        <select name="user_id" id="user_id" class="form-control">
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id == $transaction->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control">{{ $transaction->description }}</textarea>
                    </div>

                    <!-- Tanggal Transaksi -->
                    <div class="form-group">
                        <label for="transaction_date">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="{{ $transaction->transaction_date }}">
                    </div>

                    <!-- Pilih Item dan Kuantitas -->
                    <div class="form-group">
                        <label for="items">Pilih Item</label>
                        <div id="edit-items-container-{{ $transaction->id }}">
                            @foreach($transaction->items as $transactionItem)
                            <div class="d-flex mb-2">
                                <select name="items[]" class="form-control mr-2">
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == $transactionItem->id ? 'selected' : '' }}>
                                        {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} - (Stok: {{ $item->stock }})
                                    </option>
                                    @endforeach
                                </select>
                                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" min="1" value="{{ $transactionItem->pivot->quantity }}">
                                <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-success mt-2 add-item-edit" data-transaction-id="{{ $transaction->id }}">Tambah Item</button>
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
                        <p>Barang Yang Dibeli :</p>
                        <ul>
                            @foreach ($transaction->items as $item)
                            <li>{{ $item->name }} - Quantity: {{ $item->pivot->quantity }} - Price: Rp. {{ number_format($item->price, 2) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{-- @if ($transaction->status === 'success')
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#transactionModalShow{{ $transaction->id }}">
                    Cetak Receipt
                </button>
                @endif --}}
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

    document.addEventListener('DOMContentLoaded', function() {
        function updateAvailableItems(container) {
            let selectedItems = Array.from(container.querySelectorAll('select[name="items[]"]'))
                .map(select => select.value);

            container.querySelectorAll('select[name="items[]"]').forEach(select => {
                let currentValue = select.value;
                select.querySelectorAll('option').forEach(option => {
                    option.hidden = selectedItems.includes(option.value) && option.value !== currentValue;
                });
            });
        }

        // Untuk modal tambah transaksi
        document.querySelector('.add-item').addEventListener('click', function() {
            let container = document.getElementById('items-container');
            let newItem = document.createElement('div');
            newItem.classList.add('d-flex', 'mb-2');

            newItem.innerHTML = `
            <select name="items[]" class="form-control mr-2">
                <option value="" selected disabled>Pilih Item</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} ({{$item->stock}})</option>
                @endforeach
            </select>
            <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty" value="1">
            <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
        `;

            container.appendChild(newItem);
            updateAvailableItems(container);
        });

        // Untuk modal tambah transaksi (remove item)
        document.getElementById('items-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                event.target.parentElement.remove();
                updateAvailableItems(document.getElementById('items-container'));
            }
        });

        // Untuk modal edit transaksi
        document.querySelectorAll('.add-item-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                let transactionId = this.getAttribute('data-transaction-id');
                let container = document.getElementById('edit-items-container-' + transactionId);
                let newItem = document.createElement('div');
                newItem.classList.add('d-flex', 'mb-2');

                newItem.innerHTML = `
                <select name="items[]" class="form-control mr-2">
                    <option value="" selected disabled>Pilih Item</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }} ({{$item->stock}})</option>
                    @endforeach
                </select>
                <input type="number" name="quantities[]" class="form-control w-25" placeholder="Qty">
                <button type="button" class="btn btn-danger ml-2 remove-item">-</button>
            `;

                container.appendChild(newItem);
                updateAvailableItems(container);
            });
        });

        // Event delegation untuk tombol remove item (berlaku untuk semua modal)
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-item')) {
                let container = event.target.closest('#items-container, .edit-items-container');
                event.target.parentElement.remove();
                updateAvailableItems(container);
            }
        });

        // Event listener untuk memastikan hanya satu item yang bisa dipilih
        document.addEventListener('change', function(event) {
            if (event.target.matches('select[name="items[]"]')) {
                let container = event.target.closest('#items-container, .edit-items-container');
                updateAvailableItems(container);
            }
        });
    });
</script>
@endsection