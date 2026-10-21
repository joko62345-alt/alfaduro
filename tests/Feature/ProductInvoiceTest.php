<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_price_pdf_streams_a4_pdf_without_changing_product_data(): void
    {
        $product = Product::factory()->create([
            'name' => 'Kopi Arabika',
            'sku' => 'KOPI-ARABIKA',
            'price' => '32500.00',
        ]);
        $updatedAt = $product->updated_at;

        $response = $this->get(route('products.invoice', $product));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeaderContains(
                'content-disposition',
                'inline; filename=rincian-harga-kopi-arabika.pdf'
            );
        self::assertSame('%PDF', substr($response->getContent(), 0, 4));
        self::assertSame($updatedAt->toDateTimeString(), $product->fresh()->updated_at->toDateTimeString());
        self::assertSame(1, Product::query()->count());
    }

    public function test_product_price_pdf_can_be_downloaded(): void
    {
        $product = Product::factory()->create([
            'sku' => 'PRODUK-UJI',
        ]);

        $this->get(route('products.invoice.download', $product))
            ->assertOk()
            ->assertDownload('rincian-harga-produk-uji.pdf')
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_product_price_pdf_returns_not_found_for_an_unknown_product(): void
    {
        $this->get('/products/999999/invoice')->assertNotFound();
        $this->get('/products/999999/invoice/download')->assertNotFound();
    }

    public function test_invoice_view_uses_catalog_data_and_discloses_missing_transaction_details(): void
    {
        $product = Product::factory()->create([
            'name' => 'Kopi <Arabika>',
            'sku' => 'KOPI-01',
            'price' => '32500.00',
            'description' => 'Biji kopi pilihan.',
        ]);

        $this->view('invoices.invoice', [
            'product' => $product,
            'applicationName' => 'Alfaduro',
            'documentNumber' => 'PRICE-'.$product->sku,
            'generatedAt' => now(),
        ])
            ->assertSee('PRICE-KOPI-01')
            ->assertSee('Kopi &lt;Arabika&gt;', false)
            ->assertSee('Rp 32.500,00')
            ->assertSee('Kuantitas transaksi')
            ->assertSee('bukan invoice transaksi atau bukti pembayaran');
    }
}
