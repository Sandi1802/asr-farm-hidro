<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua QR - {{ $greenhouse->name }}</title>
    <!-- Phosphor Icons for branding -->
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
            min-height: 100vh;
        }

        .grid-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1.75rem;
            justify-content: center;
            max-width: 960px;
        }

        /* Container for just the QR */
        .qr-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 2px solid #e5e7eb;
            overflow: hidden;
            page-break-inside: avoid;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-sizing: border-box;
            padding: 16px;
        }

        .qr-logo-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border: 3px solid #ffffff;
            z-index: 10;
        }

        .qr-logo-overlay img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 50%;
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
            transition: all 0.2s ease;
            z-index: 1000;
        }

        .print-btn:hover {
            background: #15803d;
            transform: translateY(-2px);
        }

        /* Print Specific Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
                align-items: flex-start;
                justify-content: flex-start;
            }
            .grid-container {
                gap: 1.5rem;
                justify-content: flex-start;
                max-width: 100%;
            }
            .qr-card {
                box-shadow: none;
                border: 2px dashed #9ca3af; /* Dashed cut line */
                margin: 0;
            }
            .print-btn {
                display: none;
            }
            @page {
                size: auto;
                margin: 0mm;
            }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">
        <i class="ph ph-printer"></i> Cetak Semua QR Rak
    </button>

    <div class="grid-container">
        @foreach($greenhouse->racks as $rack)
        <div class="qr-card">
            <div class="qrcode-wrapper" data-url="{{ route('hydroponics.scan.rack', $rack->id) }}"></div>
            <div class="qr-logo-overlay">
                <img src="{{ asset('images/logo-asr.png') }}" alt="ASR Logo" onerror="this.src='https://unpkg.com/@phosphor-icons/core@2.0.8/assets/bold/plant-bold.svg';">
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
                    width: 200,
                    height: 200,
                    colorDark : "#064e3b",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            });

            // Automatically open print dialog after a short delay
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
