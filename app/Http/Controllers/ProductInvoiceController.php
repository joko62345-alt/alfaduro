<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ProductInvoiceController extends Controller
{
    public function show(Product $product): Response
    {
        return $this->pdf($product)->stream($this->filename($product));
    }

    public function download(Product $product): Response
    {
        return $this->pdf($product)->download($this->filename($product));
    }

    private function pdf(Product $product): DomPdf
    {
        return Pdf::loadView('invoices.invoice', [
            'product' => $product,
            'applicationName' => config('app.name'),
            'documentNumber' => 'PRICE-'.$product->sku,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');
    }

    private function filename(Product $product): string
    {
        return 'rincian-harga-'.Str::slug($product->sku).'.pdf';
    }
}
