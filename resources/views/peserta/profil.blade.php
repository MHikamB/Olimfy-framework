@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm" style="max-width: 700px;">
        <div class="card-header fw-bold">Edit Biodata & Profil Akademik</div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('peserta.profil.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">NIM / NISN</label>
                    <input type="text" name="nim_nisn" class="form-control" value="{{ old('nim_nisn', $profile->nim_nisn ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Asal Kampus / Sekolah</label>
                    <input type="text" name="institusi" class="form-control" value="{{ old('institusi', $profile->institusi ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenjang Pendidikan</label>
                    <select name="jenjang" class="form-select">
                        @foreach(['SMA/SMK', 'Mahasiswa', 'Umum'] as $j)
                        <option value="{{ $j }}" {{ ($profile->jenjang ?? '') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jurusan / Program Studi</label>
                    <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $profile->jurusan ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $profile->no_hp ?? '') }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection