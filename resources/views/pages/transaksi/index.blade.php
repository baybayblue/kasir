@extends('layouts.app')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Data Transaksi</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Data Transaksi</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Daftar Transaksi</h5>
                <span class="float-right">
                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i>Transaksi Baru</a>
                </span>
            </div>
            <div class="card-body">
                @include('components.alert')
                <form action="{{ route('transaksi.index') }}" method="GET" class="mb-4">
                    <div class="form-row align-items-end">
                        <div class="col-auto mb-2">
                            <div class="form-group mb-0">
                                <label for="limit" class="col-form-label mr-2">Limit:</label>
                                <select name="limit" id="limit" class="form-control" onchange="this.form.submit()">
                                    @foreach ([5, 10, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" {{ request('limit', 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-auto mb-2">
                            <label for="start_date">Dari Tanggal:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $startDate ?? '') }}">
                        </div>
                        <div class="col-auto mb-2">
                            <label for="end_date">Sampai Tanggal:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $endDate ?? '') }}">
                        </div>
                        <div class="col-auto mb-2">
                            <label for="search_query">Cari Pelanggan / No. Order:</label>
                            <input type="text" name="search_query" id="search_query" class="form-control" placeholder="Nama Pelanggan atau No. Order" value="{{ old('search_query', $searchQuery ?? '') }}">
                        </div>
                        <div class="col-auto mb-2">
                            <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Cari</button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Reset</a>
                            <a href="{{ route('transaksi.export-excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Cetak Excel</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No Order</th>
                                <th>Pelanggan</th>
                                <th>Tgl Order</th>
                                <th>Total</th>
                                <th>Uang Muka</th>
                                <th>Diskon</th>
                                <th>Sisa</th>
                                <th>Status Pembayaran</th>
                                <th>Status Pengerjaan</th>
                                <th style="width: 250px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksi as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_transaksi }}</td>
                                <td>{{ $item->pelanggan->nama ?? 'Umum' }}</td>
                                <td>{{ $item->tanggal_order->format('d/m/Y') }}</td>
                                <td>Rp{{ number_format($item->total, 2, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->uang_muka, 2, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->diskon, 2, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->sisa, 2, ',', '.') }}</td>
                                <td>
                                    @if ($item->sisa <= 0)
                                        <span class="badge badge-success">LUNAS</span>
                                    @else
                                        <span class="badge badge-warning">BELUM LUNAS</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch ($item->status_pengerjaan) {
                                            case 'menunggu export': $statusClass = 'badge-secondary'; break;
                                            case 'belum dikerjakan': $statusClass = 'badge-danger'; break;
                                            case 'proses desain': $statusClass = 'badge-info'; break;
                                            case 'proses produksi': $statusClass = 'badge-primary'; break;
                                            case 'selesai': $statusClass = 'badge-success'; break;
                                            default: $statusClass = 'badge-light'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $item->status_pengerjaan)) }}</span>
                                </td>
                                <td>
                                    {{-- Tombol "Bayar" (Pelunasan) hanya jika belum lunas --}}
                                    @if ($item->sisa > 0)
                                        <button type="button" class="btn btn-success btn-sm mb-1"
                                                onclick="showPelunasanModal('{{ $item->id }}', '{{ $item->sisa }}', '{{ $item->total }}', '{{ $item->diskon }}')">
                                            <i class="fas fa-money-bill-wave"></i> Bayar
                                        </button>
                                    @endif

                                    <div class="btn-group mb-1">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Aksi
                                        </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('transaksi.print-receipt', $item->id) }}" target="_blank">
                                                <i class="fas fa-print"></i> PRINT STRUK
                                            </a>
                                            <a class="dropdown-item" href="{{ route('transaksi.print-invoice', $item->id) }}" target="_blank">
                                                <i class="fas fa-file-invoice"></i> PRINT INVOICE
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger" type="button" onclick="confirmDelete('{{ $item->id }}')">
                                                <i class="fas fa-trash-alt"></i>HAPUS TRANSAKSI
                                            </button>
                                        </div>
                                    </div>
                                    <a href="{{ route('transaksi.edit', $item->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>

                                    {{-- Form DELETE tersembunyi untuk SweetAlert (tetap diperlukan untuk aksi hapus) --}}
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('transaksi.destroy', $item->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total Keseluruhan Transaksi:</th>
                                <th>Rp{{ number_format($totalKeseluruhanTransaksi, 2, ',', '.') }}</th>
                                <th colspan="2" class="text-right">Total Uang Muka:</th>
                                <th>Rp{{ number_format($totalUangMuka, 2, ',', '.') }}</th>
                                <th colspan="2" class="text-right">Total Piutang:</th>
                                <th colspan="1">Rp{{ number_format($totalPiutang, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pelunasan --}}
<div class="modal fade" id="pelunasanModal" tabindex="-1" role="dialog" aria-labelledby="pelunasanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pelunasanModalLabel">Pelunasan Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- Tambahkan enctype untuk upload file --}}
            <form id="formPelunasan" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="transaksi_id" id="modal_transaksi_id">

                    <div class="form-group">
                        <label>Diskon Transaksi</label>
                        <input type="text" id="modal_diskon_transaksi" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Total yang Harus Dibayar</label>
                        <input type="text" id="modal_total_harus_dibayar" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal_sisa_pembayaran">Sisa Pembayaran (Sebelum Pembayaran Ini)</label>
                        <input type="text" id="modal_sisa_pembayaran" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal_jumlah_bayar">Jumlah Bayar</label>
                        <input type="number" name="jumlah_bayar" id="modal_jumlah_bayar" class="form-control" min="0" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="metode_pembayaran">Cara Bayar</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="metode_tunai" value="tunai" checked>
                                <label class="form-check-label" for="metode_tunai">Tunai</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="metode_transfer" value="transfer_bank">
                                <label class="form-check-label" for="metode_transfer">Transfer Bank</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pembayaran" id="metode_qris" value="qris">
                                <label class="form-check-label" for="metode_qris">QRIS</label>
                            </div>
                        </div>
                    </div>

                    <div id="transfer_fields" style="display: none;">
                        <div class="form-group">
                            <label for="rekening_id">Pilih Bank</label>
                            <select name="rekening_id" id="rekening_id" class="form-control @error('rekening_id') is-invalid @enderror">
                                <option value="">Pilih Rekening Bank</option>
                                {{-- Pastikan variabel $rekening tersedia dari controller --}}
                                @foreach($rekening as $rek)
                                    <option value="{{ $rek->id }}">
                                        {{ $rek->bank }} - {{ $rek->nomor_rekening }} ({{ $rek->atas_nama }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rekening_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="bukti_pembayaran">Upload Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control-file @error('bukti_pembayaran') is-invalid @enderror">
                            @error('bukti_pembayaran')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="qris_fields" style="display: none;">
                        <div class="form-group text-center">
                            <label>Scan QRIS untuk Pembayaran</label>
                            <img id="qrisImage"
                                 src="{{ $perusahaan->qr_code ? asset('storage/' . $perusahaan->qr_code) : 'https://placehold.co/200x200/cccccc/333333?text=QRIS+Not+Available' }}"
                                 alt="QRIS Code"
                                 class="img-fluid"
                                 style="max-width: 200px; margin: 0 auto; display: none;"> 
                            <small id="qrisStatusMessage" class="form-text text-muted mt-2" style="display: none;">
                                Pastikan Anda telah mengunggah QRIS di pengaturan perusahaan.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan_pembayaran">Keterangan (Opsional)</label>
                        <textarea name="keterangan_pembayaran" id="keterangan_pembayaran" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses Pelunasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function formatRupiah(angka) {
        if (angka === null || angka === undefined || isNaN(angka)) {
            return 'Rp 0';
        }
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + ribuan;
    }

    // Fungsi untuk menampilkan modal pelunasan
    function showPelunasanModal(transaksiId, sisaPembayaran, totalTransaksi, diskonTransaksi) {
        // Reset form pelunasan di awal
        document.getElementById('formPelunasan').reset();

        document.getElementById('modal_transaksi_id').value = transaksiId;
        document.getElementById('modal_sisa_pembayaran').value = formatRupiah(parseFloat(sisaPembayaran));
        document.getElementById('modal_jumlah_bayar').value = parseFloat(sisaPembayaran); // Default jumlah bayar adalah sisa
        document.getElementById('modal_diskon_transaksi').value = formatRupiah(parseFloat(diskonTransaksi));
        document.getElementById('modal_total_harus_dibayar').value = formatRupiah(parseFloat(totalTransaksi));

        // Set default ke Tunai dan panggil togglePaymentFields untuk inisialisasi tampilan
        document.getElementById('metode_tunai').checked = true;
        togglePaymentFields(); // Panggil fungsi baru untuk mengatur tampilan awal

        document.getElementById('formPelunasan').action = `/transaksi/${transaksiId}/pelunasan`; // Sesuaikan URL aksi

        $('#pelunasanModal').modal('show');
    }

    // Event listener untuk perubahan metode pembayaran
    const metodePembayaranRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
    const transferFieldsDiv = document.getElementById('transfer_fields');
    const qrisFieldsDiv = document.getElementById('qris_fields');
    const buktiPembayaranInput = document.getElementById('bukti_pembayaran');
    const rekeningIdSelect = document.getElementById('rekening_id');
    const qrisImage = document.getElementById('qrisImage'); // Dapatkan elemen gambar QRIS
    const qrisStatusMessage = document.getElementById('qrisStatusMessage'); // Dapatkan elemen pesan status QRIS

    function togglePaymentFields() {
        const selectedMethod = document.querySelector('input[name="metode_pembayaran"]:checked').value;

        // Reset display for all fields
        transferFieldsDiv.style.display = 'none';
        qrisFieldsDiv.style.display = 'none';

        // Remove required attributes from all fields
        buktiPembayaranInput.removeAttribute('required');
        rekeningIdSelect.removeAttribute('required');

        if (selectedMethod === 'transfer_bank') {
            transferFieldsDiv.style.display = 'block';
            buktiPembayaranInput.setAttribute('required', 'required');
            rekeningIdSelect.setAttribute('required', 'required');
        } else if (selectedMethod === 'qris') {
            qrisFieldsDiv.style.display = 'block';

            // Logika untuk menampilkan atau menyembunyikan gambar/pesan QRIS
            // Dapatkan URL QRIS dari atribut src gambar
            const currentQrisSrc = qrisImage.src;

            // Cek apakah URL QRIS adalah placeholder atau URL yang valid
            if (currentQrisSrc.includes('placehold.co') || !currentQrisSrc) {
                // Jika placeholder atau kosong, sembunyikan gambar dan tampilkan pesan
                qrisImage.style.display = 'none';
                qrisStatusMessage.style.display = 'block';
            } else {
                // Jika ada URL yang valid, coba muat gambar
                // Gunakan objek Image untuk memuat gambar secara terpisah
                const img = new Image();
                img.src = currentQrisSrc;

                img.onload = () => {
                    // Jika berhasil dimuat, tampilkan gambar dan sembunyikan pesan
                    qrisImage.style.display = 'block';
                    qrisStatusMessage.style.display = 'none';
                };

                img.onerror = () => {
                    // Jika gagal dimuat, sembunyikan gambar dan tampilkan pesan
                    qrisImage.style.display = 'none';
                    qrisStatusMessage.style.display = 'block';
                };
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        metodePembayaranRadios.forEach(radio => {
            radio.addEventListener('change', togglePaymentFields);
        });

        // Panggil saat halaman dimuat untuk memastikan tampilan awal yang benar
        togglePaymentFields();

        // Optional: Tambahkan event listener untuk saat modal ditampilkan
        $('#pelunasanModal').on('show.bs.modal', function (event) {
            // Reset tampilan field setiap kali modal dibuka
            togglePaymentFields();
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function printStruk(id) {
        Swal.fire('Informasi', 'Fitur PRINT STRUK untuk transaksi ID: ' + id + ' akan segera tersedia!', 'info');
    }

    function printInvoice(id) {
        Swal.fire('Informasi', 'Fitur PRINT INVOICE untuk transaksi ID: ' + id + ' akan segera tersedia!', 'info');
    }

    $(document).ready(function() {
        $(document).on('show.bs.modal', '.modal', function() {
            var zIndex = 1040 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });

        $(document).on('hidden.bs.modal', '.modal', function() {
            $('.modal:visible').length && $(document.body).addClass('modal-open');
        });
    });
</script>
@endpush
