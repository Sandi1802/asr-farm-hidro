@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="mb-0 text-white">
                <i class="fas fa-clipboard-list me-2"></i> Laporan Pemeliharaan
            </h2>
            <p class="text-white-50 mb-0">Log aktivitas penyemprotan, pengisian nutrisi, panen, dan kerusakan.</p>
        </div>
        <div class="col-md-6 text-end">
            <form action="{{ route('hydroponics.maintenance-logs.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus SELURUH log Tanam? Data yang dihapus tidak dapat dikembalikan!');">
                @csrf
                <input type="hidden" name="type" value="tanam">
                <button type="submit" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-trash me-1"></i> Hapus Log Tanam
                </button>
            </form>
            <form action="{{ route('hydroponics.maintenance-logs.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus SELURUH log Panen? Data yang dihapus tidak dapat dikembalikan!');">
                @csrf
                <input type="hidden" name="type" value="panen">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Log Panen
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('hydroponics.maintenance-logs') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase">Tipe Aktivitas</label>
                    <select name="action_type" class="form-select border-0 bg-light">
                        <option value="">Semua Tipe</option>
                        <option value="panen" {{ request('action_type') == 'panen' ? 'selected' : '' }}>Panen</option>
                        <option value="rusak" {{ request('action_type') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="penyemprotan" {{ request('action_type') == 'penyemprotan' ? 'selected' : '' }}>Penyemprotan GH</option>
                        <option value="kuras_tandon" {{ request('action_type') == 'kuras_tandon' ? 'selected' : '' }}>Kuras Tandon</option>
                        <option value="isi_ab_mix" {{ request('action_type') == 'isi_ab_mix' ? 'selected' : '' }}>Isi Nutrisi AB Mix</option>
                        <option value="pindah_tanam" {{ request('action_type') == 'pindah_tanam' ? 'selected' : '' }}>Pindah Tanam</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control border-0 bg-light" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small text-uppercase">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control border-0 bg-light" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Filter Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tanggal & Jam</th>
                            <th>Pengguna</th>
                            <th>Tipe Aktivitas</th>
                            <th>Lokasi (GH / Rak)</th>
                            <th>Catatan & Detail</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                <div class="small text-muted">{{ $log->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="fas fa-user small"></i>
                                    </div>
                                    <span class="fw-medium">{{ $log->user->name ?? 'Sistem' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($log->action_type == 'panen')
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="fas fa-leaf me-1"></i> Panen</span>
                                @elseif($log->action_type == 'rusak')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i> Rusak</span>
                                @elseif($log->action_type == 'penyemprotan')
                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><i class="fas fa-spray-can me-1"></i> Penyemprotan</span>
                                @elseif($log->action_type == 'kuras_tandon')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1"><i class="fas fa-water me-1"></i> Kuras Tandon</span>
                                @elseif($log->action_type == 'isi_ab_mix')
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="fas fa-flask me-1"></i> Nutrisi AB Mix</span>
                                @elseif($log->action_type == 'pindah_tanam')
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="fas fa-seedling me-1"></i> Pindah Tanam</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ str_replace('_', ' ', strtoupper($log->action_type)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->loggable_type == 'App\Models\Rack')
                                    <div class="fw-semibold">{{ optional(optional($log->loggable)->greenhouse)->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">Rak: {{ optional($log->loggable)->name ?? 'N/A' }}</div>
                                @elseif($log->loggable_type == 'App\Models\Greenhouse')
                                    <div class="fw-semibold">{{ optional($log->loggable)->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">Seluruh GH</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $log->notes }}</div>
                                @if($log->details)
                                    @php $details = json_decode($log->details, true); @endphp
                                    @if(is_array($details))
                                        <div class="small text-muted mt-1">
                                            @foreach($details as $key => $val)
                                                <span class="me-3">
                                                    <strong>{{ strtoupper(str_replace('_', ' ', $key)) }}:</strong> {{ is_array($val) ? json_encode($val) : $val }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                <form action="{{ route('hydroponics.maintenance-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus log ini? Perhatian: Menghapus log ini tidak akan merubah status lubang di rak secara otomatis (hanya menghapus riwayatnya saja).');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Log">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <h5>Belum ada riwayat pemeliharaan</h5>
                                <p class="mb-0">Belum ada riwayat pemeliharaan yang tercatat.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-top">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
