<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil QRIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-header">
                        <h3>Silakan Scan QRIS Ini</h3>
                    </div>
                    <div class="card-body">
                        <p>Jumlah: <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></p>
                        <div class="my-3">
                            {{-- Gunakan paket simplesoftwareio/simple-qrcode untuk generate gambar --}}
                            {!! QrCode::size(300)->generate($qrContent) !!}
                        </div>
                        <p class="text-muted small">QRIS ini akan kedaluwarsa sesuai pengaturan.</p>
                        <a href="{{ route('qris.form') }}" class="btn btn-secondary mt-3">Buat QRIS Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
