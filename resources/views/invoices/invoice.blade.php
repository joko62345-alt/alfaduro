<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Rincian Harga Produk {{ $product->sku }}</title>
        <style>
            @page {
                margin: 36px;
            }

            body {
                color: #1f2937;
                font-family: DejaVu Sans, sans-serif;
                font-size: 11px;
                line-height: 1.5;
            }

            .header {
                border-bottom: 2px solid #4f46e5;
                margin-bottom: 24px;
                padding-bottom: 16px;
            }

            .header table,
            .items,
            .summary {
                width: 100%;
            }

            .header td:last-child {
                text-align: right;
            }

            h1 {
                color: #312e81;
                font-size: 22px;
                margin: 0;
            }

            .muted {
                color: #6b7280;
            }

            .notice {
                background: #eef2ff;
                border-left: 3px solid #4f46e5;
                margin: 16px 0 24px;
                padding: 10px 12px;
            }

            .section-title {
                color: #312e81;
                font-size: 12px;
                font-weight: bold;
                margin: 20px 0 8px;
            }

            .items {
                border-collapse: collapse;
                margin-top: 8px;
            }

            .items th,
            .items td {
                border-bottom: 1px solid #e5e7eb;
                padding: 10px 8px;
                text-align: left;
                vertical-align: top;
            }

            .items th {
                background: #f3f4f6;
                color: #374151;
            }

            .items .price {
                text-align: right;
                white-space: nowrap;
            }

            .summary {
                margin-top: 16px;
            }

            .summary td {
                padding: 5px 8px;
            }

            .summary td:last-child {
                text-align: right;
            }

            .disclaimer {
                border-top: 1px solid #d1d5db;
                color: #4b5563;
                margin-top: 28px;
                padding-top: 12px;
            }
        </style>
    </head>
    <body>
        <header class="header">
            <table>
                <tr>
                    <td>
                        <h1>Rincian Harga Produk</h1>
                        <div>{{ $applicationName }}</div>
                    </td>
                    <td>
                        <strong>Referensi: {{ $documentNumber }}</strong><br>
                        <span class="muted">Dibuat: {{ $generatedAt->format('d/m/Y H:i') }}</span>
                    </td>
                </tr>
            </table>
        </header>

        <div class="notice">
            Dokumen ini memuat informasi katalog produk.
        </div>

        <div class="section-title">Informasi produk</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th class="price">Harga satuan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if ($product->description)
                            <br><span class="muted">{{ $product->description }}</span>
                        @endif
                    </td>
                    <td>{{ $product->sku }}</td>
                    <td class="price">Rp {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Ringkasan</div>
        <table class="summary">
            <tr>
                <td>Kuantitas transaksi</td>
                <td>Tidak tersedia</td>
            </tr>
            <tr>
                <td>Subtotal dan total tagihan</td>
                <td>Tidak dihitung</td>
            </tr>
            <tr>
                <td>Status pembayaran</td>
                <td>Tidak tersedia</td>
            </tr>
        </table>

        <p class="disclaimer">
            Aplikasi saat ini belum mengelola pesanan, data pembeli, kuantitas transaksi, maupun pembayaran.
            Harga yang tercantum adalah harga katalog produk saat dokumen dibuat.
        </p>
    </body>
</html>
