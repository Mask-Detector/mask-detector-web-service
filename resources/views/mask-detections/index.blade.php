@extends('layouts.app')

@section('content')
<div class="min-vh-100" style="background: #f5f7fa;">
    <!-- Sidebar -->
    <div class="position-fixed start-0 top-0 bottom-0 d-flex flex-column align-items-center py-4"
         style="width: 80px; background: #ffff; z-index: 1000;">
        <div class="mb-5 text-center">
            <img src="{{ asset('images/logo.png') }}"
                alt="Logo Deteksi Mask"
                style="height: 60px; object-fit: contain;">
        </div>
        <nav class="d-flex flex-column gap-4">
            <a href="#" class="text-center text-decoration-none" style="color: #21130d;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
                <div class="small mt-1">Dashboard</div>
            </a>
            <a href="#" class="text-center text-decoration-none" style="color: #21130d;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <div class="small mt-1">Database</div>
            </a>
            <a href="#" class="text-center text-decoration-none" style="color: #21130d;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                </svg>
                <div class="small mt-1">Search</div>
            </a>
        </nav>
    </div>

        <div class="p-4">
            <!-- Section Header -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span style="color: #7f8c8d; font-weight: 500;">Dashboard</span>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row text-center mb-4 g-3">
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <h5 class="text-muted mb-3 fw-normal">Total Deteksi Hari Ini</h5>
                        <h2 class="fw-bold" style="color: #2c3e50;">{{ $todayTotal }}</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <h5 class="text-muted mb-3 fw-normal">Pakai Masker</h5>
                        <h2 class="text-success fw-bold">{{ $todayMask }}</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <h5 class="text-muted mb-3 fw-normal">Tidak Pakai Masker</h5>
                        <h2 class="text-danger fw-bold">{{ $todayNoMask }}</h2>
                    </div>
                </div>
            </div>

            <!-- Camera Feed and Table -->
            <div class="row g-4">
                <!-- Live Camera -->
                <div class="col-md-6">
                    <div class="bg-white rounded-3 overflow-hidden" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="d-flex align-items-center justify-content-between p-3"
                             style="border-bottom: 1px solid #e5e7eb; background: #2c3e50;">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                    <circle cx="12" cy="13" r="4"/>
                                </svg>
                                <span class="text-white fw-semibold">Camera 2C9</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-white">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>Arcadia</span>
                            </div>
                        </div>
                        <div class="p-3" style="background: #1a1a1a;">
                            <img src="http://127.0.0.1:5000/video"
                                 style="width: 100%; display: block; border-radius: 8px;">
                            <div class="d-flex align-items-center justify-content-between mt-3 text-white">
                                <div class="d-flex align-items-center gap-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                x<span id="tanggal" class="small d-block"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span id="waktu" class="fw-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="col-md-6">
                    <div class="bg-white rounded-3 overflow-hidden" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08); height: 100%;">
                        <div class="d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid #e5e7eb;">
                            <div>
                                <span class="text-muted small">{{ $detections->total() }} records</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary" style="border-radius: 6px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="4" y1="6" x2="20" y2="6"/>
                                        <line x1="4" y1="12" x2="20" y2="12"/>
                                        <line x1="4" y1="18" x2="20" y2="18"/>
                                    </svg>
                                </button>
                                <button class="btn btn-sm" style="background: #27ae60; color: white; border: none; border-radius: 6px;">
                                    Export Excel
                                </button>
                            </div>
                        </div>
                        <div style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-borderless mb-0">
                                <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                                    <tr style="border-bottom: 2px solid #e5e7eb;">
                                        <th class="py-2 text-muted small fw-semibold">S.no</th>
                                        <th class="py-2 text-muted small fw-semibold">Plate No.</th>
                                        <th class="py-2 text-muted small fw-semibold">Tipe</th>
                                        <th class="py-2 text-muted small fw-semibold">Tanggal Deteksi</th>
                                        <th class="py-2 text-muted small fw-semibold">Waktu</th>
                                        <th class="py-2 text-muted small fw-semibold">Site</th>
                                        <th class="py-2 text-muted small fw-semibold">Gambar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detections as $index => $d)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td class="py-3 small">{{ $index + 1 }}</td>
                                        <td class="py-3 small fw-semibold" style="color: #2c3e50;">{{ $d->wearing_mask }}</td>
                                        <td class="py-3 small text-muted">
                                            @if($d->wearing_mask > 0) With Mask @else No Mask @endif
                                        </td>
                                        <td class="py-3 small">{{ $d->detected_at }}</td>
                                        <td class="py-3 small">{{ $d->confidence_avg }}%</td>
                                        <td class="py-3 small">Site A</td>
                                        <td class="py-3">
                                            @if($d->image_path)
                                                <img src="{{ asset('storage/'.$d->image_path) }}"
                                                     style="width: 60px; height: 30px; object-fit: cover; border-radius: 4px; border: 1px solid #e5e7eb;">
                                            @else
                                                <div class="badge" style="background: #2c3e50; color: white; font-family: monospace; padding: 0.25rem 0.5rem;">
                                                    IMG{{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Pagination -->
            <div class="mt-4">
                {{ $detections->links() }}
            </div> --}}
        </div>
    </div>
</div>

<style>
    body {
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .pagination {
        margin: 0;
    }

    .page-link {
        border: 1px solid #e5e7eb;
        color: #2c3e50;
        background: white;
        margin: 0 0.15rem;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
    }

    .page-link:hover {
        background: #3498db;
        border-color: #3498db;
        color: white;
    }

    .page-item.active .page-link {
        background: #2c3e50;
        border-color: #2c3e50;
        color: white;
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>

<script>
    function updateDateTime() {
        const now = new Date();

        // Format tanggal: dd/MMM/yyyy
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        const tanggal = now.toLocaleDateString('en-GB', options).replace(/ /g, '/');

        // Format jam: HH:mm:ss
        const waktu = now.toLocaleTimeString('en-GB', { hour12: false });

        document.getElementById("tanggal").textContent = tanggal;
        document.getElementById("waktu").textContent = waktu;
    }

    // Jalankan setiap 1 detik
    setInterval(updateDateTime, 1000);
    // Jalankan pertama kali
    updateDateTime();
</script>
@endsection
