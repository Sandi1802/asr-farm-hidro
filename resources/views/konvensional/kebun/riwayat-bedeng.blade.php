@extends('layouts.app')

@section('title', 'Riwayat Bedeng')

@section('content')
<div class="project-hero">
    <div class="hero-content">
        <h1><i class="ph ph-clock-counter-clockwise"></i> Riwayat Bedeng: {{ $bedeng->kode }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0 justify-content-center">
                <li class="breadcrumb-item"><a href="#" class="text-white opacity-75">{{ $bedeng->pola->zona->lahan->nama }}</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-white opacity-75">{{ $bedeng->pola->zona->nama }}</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-white opacity-75">{{ $bedeng->pola->nama }}</a></li>
                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">{{ $bedeng->kode }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <!-- Status Saat Ini -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ph ph-info"></i> Status Saat Ini</h5>
            @if($bedeng->tanamAktif)
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ strtoupper($bedeng->status_tampilan) }}</span>
            @else
                <span class="badge bg-secondary px-3 py-2 rounded-pill">KOSONG</span>
            @endif
        </div>
        <div class="card-body">
            @if($bedeng->tanamAktif)
                @php $aktif = $bedeng->tanamAktif; @endphp
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="text-success fw-bold">{{ $aktif->tanaman->nama }} <small class="text-muted fs-6">({{ $aktif->tanaman->varietas }})</small></h4>
                        <p class="mb-1"><i class="ph ph-calendar text-primary"></i> <strong>Tgl Tanam:</strong> {{ \Carbon\Carbon::parse($aktif->tanggal_tanam)->format('d M Y') }}</p>
                        <p class="mb-1"><i class="ph ph-plant text-success"></i> <strong>Jml Tanam:</strong> {{ $aktif->jumlah_tanam }} bibit</p>
                        <p class="mb-0"><i class="ph ph-clock text-warning"></i> <strong>Umur:</strong> {{ $aktif->umur_hari }} hari</p>
                    </div>
                    <div class="col-md-6 border-start">
                        <p class="mb-1"><strong>Est. Panen:</strong> {{ \Carbon\Carbon::parse($aktif->estimasi_tanggal_panen)->format('d M Y') }}</p>
                        <p class="mb-1"><strong>Est. Hasil:</strong> {{ $aktif->estimasi_hasil }} {{ $aktif->tanaman->satuan_hasil }}</p>
                        <p class="mb-0 text-primary"><strong>Telah Dipanen:</strong> {{ $aktif->total_panen }} {{ $aktif->tanaman->satuan_hasil }}</p>
                    </div>
                </div>
            @else
                <p class="text-muted mb-0">Bedeng sedang kosong dan siap untuk ditanami kembali.</p>
            @endif
        </div>
    </div>

    <!-- Timeline Riwayat -->
    <h4 class="mb-4"><i class="ph ph-list-dashes"></i> Riwayat Siklus Tanam</h4>
    
    <div class="timeline px-2">
        @forelse($riwayat as $rekam)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="ph ph-leaf text-success"></i> {{ $rekam->tanaman->nama }}
                    </h5>
                    @if($rekam->status == 'selesai')
                        <span class="badge bg-success"><i class="ph ph-check-circle"></i> Selesai</span>
                    @elseif($rekam->status == 'gagal')
                        <span class="badge bg-danger"><i class="ph ph-x-circle"></i> Gagal</span>
                    @else
                        <span class="badge bg-primary"><i class="ph ph-spinner"></i> {{ ucfirst($rekam->status) }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="mb-1 text-muted small">Periode Tanam</p>
                            <strong>{{ \Carbon\Carbon::parse($rekam->tanggal_tanam)->format('d M Y') }}</strong>
                            <i class="ph ph-arrow-right mx-1"></i>
                            <strong>{{ $rekam->status == 'selesai' || $rekam->status == 'gagal' ? \Carbon\Carbon::parse($rekam->updated_at)->format('d M Y') : 'Sekarang' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 text-muted small">Jumlah Tanam</p>
                            <strong>{{ $rekam->jumlah_tanam }} bibit</strong>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 text-muted small">Pencapaian Hasil</p>
                            @php
                                $persen = $rekam->estimasi_hasil > 0 ? round(($rekam->total_panen / $rekam->estimasi_hasil) * 100) : 0;
                                $color = $persen >= 100 ? 'success' : ($persen >= 75 ? 'warning' : 'danger');
                            @endphp
                            <strong>{{ $rekam->total_panen }} / {{ $rekam->estimasi_hasil }} {{ $rekam->tanaman->satuan_hasil }}</strong>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-{{ $color }}" style="width: {{ min(100, $persen) }}%"></div>
                            </div>
                        </div>
                    </div>

                    @if($rekam->catatan || $rekam->alasan_gagal)
                        <div class="alert {{ $rekam->status == 'gagal' ? 'alert-danger' : 'alert-secondary' }} p-2 mb-3">
                            <i class="ph ph-note"></i> <strong>Catatan:</strong> {{ $rekam->alasan_gagal ?? $rekam->catatan }}
                        </div>
                    @endif

                    @if($rekam->panen->count() > 0)
                        <h6 class="border-bottom pb-2 mt-3"><i class="ph ph-basket"></i> Rekam Panen</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Hasil</th>
                                        <th>Kualitas</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekam->panen as $p)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($p->tanggal_panen)->format('d/m/Y') }}</td>
                                            <td class="fw-bold">{{ $p->jumlah_hasil }} {{ $p->satuan }}</td>
                                            <td>Grade {{ $p->kualitas }}</td>
                                            <td>{{ $p->catatan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small italic"><i class="ph ph-info"></i> Belum ada rekam panen untuk siklus ini.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">Belum ada riwayat penanaman pada bedeng ini.</div>
        @endforelse
    </div>
</div>
@endsection
