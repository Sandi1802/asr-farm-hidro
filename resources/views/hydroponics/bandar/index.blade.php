@extends('layouts.app')

@section('content')
<div class="content-header" style="margin-bottom: 2rem;">
    <h2>Pusat Distribusi (Pengepul Sayuran)</h2>
    <p class="text-muted">Pantau sisa stok dan riwayat pergerakan fisik barang.</p>
</div>

<!-- Action Buttons -->
<div class="mb-4" style="display: flex; gap: 1rem;">
    <a href="{{ route('hydroponics.bandar.transactions') }}" class="btn btn-primary" style="background: var(--asr-green); border:none; padding: 0.6rem 1.2rem; font-weight: 500;"><i class="ph ph-swap"></i> Catat Transaksi Baru</a>
    <a href="{{ route('hydroponics.bandar.products') }}" class="btn btn-light" style="background: white; border: 1px solid #e5e7eb; padding: 0.6rem 1.2rem; color: #374151; font-weight: 500;"><i class="ph ph-package"></i> Kelola Sayuran</a>
    <a href="{{ route('hydroponics.bandar.partners') }}" class="btn btn-light" style="background: white; border: 1px solid #e5e7eb; padding: 0.6rem 1.2rem; color: #374151; font-weight: 500;"><i class="ph ph-users"></i> Kelola Petani/Mitra</a>
</div>

<!-- Global Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-4" style="border:none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid var(--asr-green);">
            <p style="color: #6b7280; margin: 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Sisa Stok</p>
            <h3 style="margin: 0; margin-top: 0.5rem; font-weight: bold; color: #111827;">{{ $totalStock }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4" style="border:none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid #3b82f6;">
            <p style="color: #6b7280; margin: 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Masuk</p>
            <h3 style="margin: 0; margin-top: 0.5rem; font-weight: bold; color: #111827;">{{ $totalInQty }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4" style="border:none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid #f59e0b;">
            <p style="color: #6b7280; margin: 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Keluar</p>
            <h3 style="margin: 0; margin-top: 0.5rem; font-weight: bold; color: #111827;">{{ $totalOutQty }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4" style="border:none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid #ef4444;">
            <p style="color: #6b7280; margin: 0; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Terbuang</p>
            <h3 style="margin: 0; margin-top: 0.5rem; font-weight: bold; color: #111827;">{{ $totalWastedQty }}</h3>
        </div>
    </div>
</div>

<!-- Per Product Stats Table -->
<div class="card p-0" style="border:none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
    <div class="card-header bg-white" style="padding: 1.5rem; border-bottom: 1px solid #f3f4f6;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: bold; color: #111827;">Rincian Stok Per Sayuran</h3>
    </div>
    <div class="table-responsive">
        <table class="table datatable mb-0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f9fafb;">
                <tr>
                    <th style="padding: 1rem 1.5rem; color: #4b5563; font-weight: 600; border-bottom: 1px solid #e5e7eb;">Sayuran / Komoditas</th>
                    <th style="padding: 1rem 1.5rem; color: #4b5563; font-weight: 600; border-bottom: 1px solid #e5e7eb; text-align: center;">Total Masuk</th>
                    <th style="padding: 1rem 1.5rem; color: #4b5563; font-weight: 600; border-bottom: 1px solid #e5e7eb; text-align: center;">Total Keluar</th>
                    <th style="padding: 1rem 1.5rem; color: #4b5563; font-weight: 600; border-bottom: 1px solid #e5e7eb; text-align: center;">Terbuang / Rusak</th>
                    <th style="padding: 1rem 1.5rem; color: #4b5563; font-weight: 600; border-bottom: 1px solid #e5e7eb; text-align: right;">Sisa Stok Gudang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productStats as $stat)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 1rem 1.5rem; font-weight: 500; color: #111827;">
                        {{ $stat['name'] }}
                    </td>
                    <td style="padding: 1rem 1.5rem; text-align: center; color: #3b82f6;">{{ $stat['inQty'] }} {{ $stat['unit'] }}</td>
                    <td style="padding: 1rem 1.5rem; text-align: center; color: #f59e0b;">{{ $stat['outQty'] }} {{ $stat['unit'] }}</td>
                    <td style="padding: 1rem 1.5rem; text-align: center; color: #ef4444;">{{ $stat['wastedQty'] }} {{ $stat['unit'] }}</td>
                    <td style="padding: 1rem 1.5rem; text-align: right;">
                        <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; background: {{ $stat['stock'] > 0 ? '#dcfce7' : '#fee2e2' }}; color: {{ $stat['stock'] > 0 ? '#166534' : '#991b1b' }};">
                            {{ $stat['stock'] }} {{ $stat['unit'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 2rem;">Belum ada data komoditas sayuran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
