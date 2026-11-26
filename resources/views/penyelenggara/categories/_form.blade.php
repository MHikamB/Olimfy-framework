@php
    $category = $category ?? null;
@endphp

@if ($errors->any())
    <div class="alert alert-danger mb-4" role="alert">
        <h2 class="h6 fw-bold mb-2">
            <i class="bi bi-exclamation-circle-fill me-1" aria-hidden="true"></i>
            Periksa kembali isian formulir Anda:
        </h2>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-tags me-2" aria-hidden="true"></i>
                Informasi Kategori
            </h2>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nama_kategori" class="form-label fw-semibold">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           id="nama_kategori"
                           name="nama_kategori"
                           class="form-control @error('nama_kategori') is-invalid @enderror"
                           value="{{ old('nama_kategori', $category?->nama_kategori) }}"
                           placeholder="Contoh: Teknologi"
                           required>
                    @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="slug" class="form-label fw-semibold">
                        Slug <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           id="slug"
                           name="slug"
                           class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $category?->slug) }}"
                           placeholder="teknologi"
                           required>
                    <div class="form-text">Dibuat otomatis dari nama, bisa disesuaikan.</div>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="ikon" class="form-label fw-semibold">
                        Ikon Bootstrap <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" id="ikonPreview">
                            <i class="bi {{ old('ikon', $category?->ikon ?? 'bi-tag') }}" aria-hidden="true"></i>
                        </span>
                        <input type="text"
                               id="ikon"
                               name="ikon"
                               class="form-control @error('ikon') is-invalid @enderror"
                               value="{{ old('ikon', $category?->ikon ?? 'bi-tag') }}"
                               placeholder="bi-tag"
                               required>
                        @error('ikon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">Contoh: bi-tag, bi-cpu, bi-palette, bi-briefcase, bi-lightbulb.</div>
                </div>

                <div class="col-12">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea id="deskripsi"
                              name="deskripsi"
                              rows="4"
                              class="form-control @error('deskripsi') is-invalid @enderror"
                              placeholder="Jelaskan cakupan kategori ini (opsional).">{{ old('deskripsi', $category?->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('penyelenggara.kategori.index') }}" class="btn btn-outline-secondary">
            Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const namaInput = document.getElementById('nama_kategori');
            const slugInput = document.getElementById('slug');
            const ikonInput = document.getElementById('ikon');
            const ikonPreview = document.getElementById('ikonPreview');

            if (namaInput && slugInput) {
                if (slugInput.value === '') {
                    slugInput.dataset.auto = 'true';
                }

                namaInput.addEventListener('input', () => {
                    if (slugInput.dataset.auto !== 'true') {
                        return;
                    }

                    slugInput.value = namaInput.value
                        .toLowerCase()
                        .normalize('NFKD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                });

                slugInput.addEventListener('input', () => {
                    slugInput.dataset.auto = 'false';
                });
            }

            if (ikonInput && ikonPreview) {
                ikonInput.addEventListener('input', () => {
                    const ikon = ikonInput.value.trim() || 'bi-tag';
                    ikonPreview.innerHTML = `<i class="bi ${ikon}" aria-hidden="true"></i>`;
                });
            }
        });
    </script>
@endpush
