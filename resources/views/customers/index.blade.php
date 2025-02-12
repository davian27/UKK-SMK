@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>Daftar Customer</h1>

        @hasrole('admin')
            <!-- <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Pelanggan</button> -->
        @endhasrole
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">>
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Pelanggan Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Foto</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Tambah Pelanggan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @hasrole('admin')
            <table class="table table-bordered">
                <tbody>
                    <div class="row">
                        @foreach ($customers as $index => $customer)
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <img src="{{ $customer->photo ? asset('storage/' . $customer->photo) : asset('images/png.png') }}"
                                        class="card-img-top pt-3" alt="Foto Barang" style="height: 150px; object-fit: contain;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $customer->name }}</h5>
                                        <p class="card-text">{{ $customer->email }}</p>
                                        <p class="card-text">{{ $customer->phone }}</p>
                                        @hasrole('admin')
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $customer->id }}">Edit</button>
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $customer->id }}">Delete</button>
                                            </div>
                                        @endhasrole
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal{{ $customer->id }}" tabindex="-1"
                                aria-labelledby="editModalLabel{{ $customer->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('users.update', $customer->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $customer->id }}">Edit Pelanggan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        value="{{ $customer->name }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        value="{{ $customer->email }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">No. Telepon</label>
                                                    <input type="text" class="form-control" id="phone" name="phone"
                                                        value="{{ $customer->phone }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Delete -->
                            <div class="modal fade" id="deleteModal{{ $customer->id }}" tabindex="-1"
                                aria-labelledby="deleteModalLabel{{ $customer->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $customer->id }}">Konfirmasi Hapus
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus pelanggan <strong>{{ $customer->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('users.destroy', $customer->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </tbody>
            </table>
        @endhasrole

        @hasrole('customer')
            <div class="container mt-4">
                <div class="row">
                    @foreach ($customers as $index => $customer)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="{{ $customer->photo ? asset('storage/' . $customer->photo) : asset('images/png.png') }}"
                                    class="card-img-top pt-3" alt="Foto Barang" style="height: 150px; object-fit: contain;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $customer->name }}</h5>
                                    <p class="card-text"><strong>Email:</strong> {{ $customer->email }}</p>
                                    <p class="card-text"><strong>No. Telepon:</strong> {{ $customer->phone }}</p>

                                    @hasrole('admin')
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $customer->id }}">Edit</button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $customer->id }}">Delete</button>
                                        </div>
                                    @endhasrole
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal{{ $customer->id }}" tabindex="-1"
                            aria-labelledby="editModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('users.update', $customer->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $customer->id }}">Edit Pelanggan
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ $customer->name }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ $customer->email }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">No. Telepon</label>
                                                <input type="text" class="form-control" id="phone" name="phone"
                                                    value="{{ $customer->phone }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- Modal Delete -->
                        <div class="modal fade" id="deleteModal{{ $customer->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $customer->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $customer->id }}">Konfirmasi Hapus
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus pelanggan <strong>{{ $customer->name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('users.destroy', $customer->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endhasrole
    </div>
@endsection
