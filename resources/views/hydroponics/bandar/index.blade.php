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
<div class="dashboard-stats" style="margin-bottom: 2rem;">
    <div class="stat-big-card sbc-dark-green" style="cursor: default;">
        <div>
            <div class="sbc-value">{{ $totalStock }}</div>
            <div class="sbc-label">Total Sisa Stok</div>
            <div style="font-size:0.75rem; color: rgba(255,255,255,0.85); font-weight:600; margin-top:0.35rem; letter-spacing:0.3px;">Stok di Gudang</div>
        </div>
        <i class="ph ph-package sbc-icon"></i>
    </div>
    
    <div class="stat-big-card sbc-teal-farm" style="cursor: default;">
        <div>
            <div class="sbc-value">{{ $totalInQty }}</div>
            <div class="sbc-label">Total Masuk</div>
            <div style="font-size:0.75rem; color: rgba(255,255,255,0.85); font-weight:600; margin-top:0.35rem; letter-spacing:0.3px;">Dari Petani/Mitra</div>
        </div>
        <i class="ph ph-trend-up sbc-icon"></i>
    </div>
    
    <div class="stat-big-card sbc-gold" style="cursor: default;">
        <div>
            <div class="sbc-value">{{ $totalOutQty }}</div>
            <div class="sbc-label">Total Keluar</div>
            <div style="font-size:0.75rem; color: rgba(255,255,255,0.85); font-weight:600; margin-top:0.35rem; letter-spacing:0.3px;">Ke Pasar/Pembeli</div>
        </div>
        <i class="ph ph-trend-down sbc-icon"></i>
    </div>
    
    <div class="stat-big-card sbc-rust" style="cursor: default;">
        <div>
            <div class="sbc-value">{{ $totalWastedQty }}</div>
            <div class="sbc-label">Total Terbuang</div>
            <div style="font-size:0.75rem; color: rgba(255,255,255,0.85); font-weight:600; margin-top:0.35rem; letter-spacing:0.3px;">Rusak/Kadet</div>
        </div>
        <i class="ph ph-trash sbc-icon"></i>
    </div>
</div>

<!-- Per Product Stats Table -->
<div class="card" style="border:none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
    <div class="card-header bg-white" style="padding: 1.5rem; border-bottom: 1px solid #f3f4f6;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: bold; color: #111827;">Rincian Stok Per Sayuran</h3>
    </div>
    <div class="table-responsive" style="padding: 1.5rem;">
        <table class="table datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th class="dt-no">NO</th>
                    <th>Sayuran / Komoditas</th>
                    <th style="text-align: center;">Total Masuk</th>
                    <th style="text-align: center;">Total Keluar</th>
                    <th style="text-align: center;">Terbuang / Rusak</th>
                    <th style="text-align: center;">Sisa Stok Gudang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productStats as $stat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight: 600;">
                        {{ $stat['name'] }}
                    </td>
                    <td style="text-align: center; color: #3b82f6; font-weight: 600;">{{ $stat['inQty'] }} {{ $stat['unit'] }}</td>
                    <td style="text-align: center; color: #f59e0b; font-weight: 600;">{{ $stat['outQty'] }} {{ $stat['unit'] }}</td>
                    <td style="text-align: center; color: #ef4444; font-weight: 600;">{{ $stat['wastedQty'] }} {{ $stat['unit'] }}</td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: {{ $stat['stock'] > 0 ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $stat['stock'] > 0 ? '#16a34a' : '#ef4444' }}; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600;">
                            {{ $stat['stock'] }} {{ $stat['unit'] }}
                        </span>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

