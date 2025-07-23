@extends('layouts.app')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-plus-circle mr-2"></i>Tambah Akun Baru</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jenis-akun.index') }}">Jenis Akun</a></li>
            <li class="breadcrumb-item active">Tambah Akun</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-pencil-alt mr-2"></i>Form Tambah Akun</h3>
            </div>
            <form action="{{ route('jenis-akun.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_akun">Nama Akun <span class="text-danger">*</span></label>
                        <input type="text" name="nama_akun" id="nama_akun" class="form-control @error('nama_akun') is-invalid @enderror" value="{{ old('nama_akun') }}" required>
                        @error('nama_akun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="jenis">Jenis Akun <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-control @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Harta" {{ old('jenis') == 'Harta' ? 'selected' : '' }}>Harta</option>
                            <option value="Utang" {{ old('jenis') == 'Utang' ? 'selected' : '' }}>Utang</option>
                            <option value="Modal" {{ old('jenis') == 'Modal' ? 'selected' : '' }}>Modal</option>
                            <option value="Pendapatan" {{ old('jenis') == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                            <option value="Beban" {{ old('jenis') == 'Beban' ? 'selected' : '' }}>Beban</option>
                        </select>
                        @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('jenis-akun.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
