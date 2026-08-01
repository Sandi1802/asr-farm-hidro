<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code Green House - ASR FARM</title>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .grid-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1.75rem;
            justify-content: center;
            max-width: 960px;
        }

        /* Container for each Greenhouse QR label card */
        .gh-label-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            width: 220px;
            border: 2px solid #16a34a;
            overflow: hidden;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            box-sizing: border-box;
        }

        .gh-card-header {
            width: 100%;
            background: linear-gradient(135deg, #15803d, #16a34a);
            color: white;
            padding: 0.75rem 1rem;
            text-align: center;
            box-sizing: border-box;
        }

        .brand-subtitle {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.9;
            margin: 0;
        }

        .gh-title {
            font-weight: 800;
            font-size: 1.15rem;
            margin: 0.2rem 0 0 0;
            line-height: 1.2;
        }

        .gh-card-body {
            padding: 1.25rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }

        .stats-badge {
            background: #dcfce7;
            color: #15803d;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* QR Code Wrapper with Central Logo Overlay */
        .qr-wrapper-relative {
            position: relative;
            display: inline-block;
            background: white;
            padding: 8px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .qr-logo-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 38px;
            height: 38px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            border: 2px solid #ffffff;
            z-index: 10;
        }

        .qr-logo-overlay img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            border-radius: 50%;
        }

        .footer-text {
            font-size: 0.7rem;
            font-weight: 600;
            color: #6b7280;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .print-btn {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: #16a34a;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
            z-index: 1000;
            transition: background 0.2s;
        }
        .print-btn:hover {
            background: #15803d;
        }

        /* Print Specific Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }
            .grid-container {
                gap: 1.25rem;
                justify-content: flex-start;
                max-width: 100%;
            }
            .gh-label-card {
                box-shadow: none;
                border: 2px dashed #16a34a; /* Cut lines */
                border-radius: 12px;
                width: 210px;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">
        <i class="ph ph-printer"></i> Cetak Semua QR Code GH
    </button>

    <div class="grid-container">
        @foreach($greenhouses as $gh)
        <div class="gh-label-card">
            <div class="gh-card-header">
                <div class="brand-subtitle">🌿 ASR FARM · MONITORING</div>
                <h2 class="gh-title">{{ $gh->name }}</h2>
            </div>
            
            <div class="gh-card-body">
                <div class="stats-badge">
                    <i class="ph ph-squares-four"></i> {{ $gh->racks_count ?? $gh->racks->count() }} Rak · {{ number_format($gh->holes_count ?? 0) }} Lubang
                </div>

                <div class="qr-wrapper-relative">
                    <div class="qrcode-wrapper" id="qr-gh-{{ $gh->id }}" data-url="{{ route('hydroponics.scan.gh', $gh->id) }}"></div>
                    <div class="qr-logo-overlay">
                        <img src="{{ asset('images/logo-asr.png') }}" alt="ASR Logo" onerror="this.src='https://unpkg.com/@phosphor-icons/core@2.0.8/assets/bold/plant-bold.svg';">
                    </div>
                </div>

                <p class="footer-text">
                    <i class="ph ph-qr-code"></i> Scan untuk Akses GH
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- qrcode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const wrappers = document.querySelectorAll('.qrcode-wrapper');
            
            wrappers.forEach(wrapper => {
                const url = wrapper.getAttribute('data-url');
                new QRCode(wrapper, {
                    text: url,
                    width: 150,
                    height: 150,
                    colorDark : "#064e3b",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            });

            // Auto trigger print after short delay for QR render
            setTimeout(() => {
                window.print();
            }, 850);
        });
    </script>
</body>
</html>
