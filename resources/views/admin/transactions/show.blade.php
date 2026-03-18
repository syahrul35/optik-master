@extends('layouts.admin')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">

<div class="card mb-3" id="struk">
    <div class="card-body p-4">
        {{-- Header Struk --}}
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-0">Optik Perkasa</h5>
            <div class="text-muted small">Jl. Contoh No. 1, Surabaya</div>
            <div class="text-muted small">Telp: 031-xxxxxxx</div>
            <hr>
            <div class="fw-bold">STRUK PEMBAYARAN</div>
        </div>

        {{-- Info Transaksi --}}
        <div class="row mb-3">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0" style="font-size:.85rem">
                    <tr><td class="text-muted ps-0">No. Transaksi</td><td>: <strong>{{ $transaction->no_transaksi }}</strong></td></tr>
                    <tr><td class="text-muted ps-0">Tanggal</td><td>: {{ $transaction->created_at->format('d M Y H:i') }}</td></tr>
                    <tr><td class="text-muted ps-0">Kasir</td><td>: {{ $transaction->kasir->name ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0" style="font-size:.85rem">
                    <tr><td class="text-muted ps-0">Pasien</td><td>: {{ $transaction->patient->nama ?? 'Umum' }}</td></tr>
                    <tr><td class="text-muted ps-0">No. RM</td><td>: {{ $transaction->patient->no_rm ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Metode</td><td>: {{ ucfirst($transaction->metode_bayar) }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Items --}}
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light">
                <tr>
                    <th>Produk</th><th class="text-center">Qty</th>
                    <th class="text-end">Harga</th><th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->nama_produk }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">Rp {{ number_format($item->harga_satuan,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Total --}}
        <table class="table table-sm table-borderless" style="font-size:.9rem">
            <tr>
                <td class="text-end text-muted">Subtotal</td>
                <td class="text-end" style="width:150px">Rp {{ number_format($transaction->total_harga,0,',','.') }}</td>
            </tr>
            @if($transaction->diskon_nominal > 0)
            <tr>
                <td class="text-end text-muted">
                    Diskon{{ $transaction->diskon_persen > 0 ? ' ('.$transaction->diskon_persen.'%)' : '' }}
                </td>
                <td class="text-end text-danger">- Rp {{ number_format($transaction->diskon_nominal,0,',','.') }}</td>
            </tr>
            @endif
            <tr class="fw-bold border-top">
                <td class="text-end">Total Bayar</td>
                <td class="text-end fs-5">Rp {{ number_format($transaction->total_bayar,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="text-end text-muted">Bayar</td>
                <td class="text-end">Rp {{ number_format($transaction->bayar,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="text-end text-muted">Kembalian</td>
                <td class="text-end">Rp {{ number_format($transaction->kembalian,0,',','.') }}</td>
            </tr>
        </table>

        <div class="text-center mt-3 text-muted small">
            <i class="bi bi-heart-fill text-danger"></i> Terima kasih telah berbelanja di Optik Perkasa
        </div>
    </div>
</div>

<div class="d-flex gap-2 justify-content-center">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i>Cetak Struk
    </button>
    <a href="{{ route('transactions.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i>Transaksi Baru
    </a>
    @if(auth()->user()->hasAnyRole(['super_admin','admin']))
    @if($transaction->status !== 'batal')
    <form method="POST" action="{{ route('transactions.cancel', $transaction) }}"
          onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
        @csrf @method('PATCH')
        <button class="btn btn-outline-danger">
            <i class="bi bi-x-circle me-1"></i>Batalkan
        </button>
    </form>
    @endif
    @endif
    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

</div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .d-flex.gap-2.justify-content-center { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .content { padding: 0 !important; }
}
</style>
@endpush
