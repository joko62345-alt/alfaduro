<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_edit_forms_render_multipart_upload_fields(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.create'))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="image"', false);

        $this->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('Ganti gambar (opsional)');
    }

    public function test_creating_product_stores_a_resized_webp_image(): void
    {
        Storage::fake('public');

        $response = $this->post(route('products.store'), [
            ...$this->productData(),
            'image' => UploadedFile::fake()->image('product.jpg', 1600, 1200),
        ]);

        $product = Product::query()->sole();

        $response->assertRedirect(route('products.show', $product));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Uji',
            'sku' => 'SKU-PRODUK-UJI',
            'image_path' => $product->image_path,
        ]);
        Storage::disk('public')->assertExists($product->image_path);

        $image = getimagesize(Storage::disk('public')->path($product->image_path));

        self::assertIsArray($image);
        self::assertSame('image/webp', $image['mime']);
        self::assertLessThanOrEqual(1200, $image[0]);
        self::assertLessThanOrEqual(1200, $image[1]);
    }

    public function test_rejects_an_unsupported_image_format(): void
    {
        Storage::fake('public');

        $response = $this->from(route('products.create'))
            ->post(route('products.store'), [
                ...$this->productData(),
                'image' => UploadedFile::fake()->create('product.gif', 100, 'image/gif'),
            ]);

        $response->assertSessionHasErrors('image');
        self::assertSame(0, Product::query()->count());
        self::assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_rejects_an_image_larger_than_two_megabytes(): void
    {
        Storage::fake('public');

        $response = $this->from(route('products.create'))
            ->post(route('products.store'), [
                ...$this->productData(),
                'image' => UploadedFile::fake()->image('product.jpg', 10, 10)->size(2049),
            ]);

        $response->assertSessionHasErrors('image');
        self::assertSame(0, Product::query()->count());
    }

    public function test_replacing_product_image_removes_the_old_file(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image_path' => 'products/old-image.webp',
        ]);
        Storage::disk('public')->put($product->image_path, 'old-image');

        $response = $this->put(route('products.update', $product), [
            ...$this->productData(),
            'sku' => $product->sku,
            'image' => UploadedFile::fake()->image('replacement.png', 400, 300),
        ]);

        $product->refresh();

        $response->assertRedirect(route('products.show', $product));
        self::assertNotSame('products/old-image.webp', $product->image_path);
        Storage::disk('public')->assertMissing('products/old-image.webp');
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_replacing_image_does_not_delete_a_file_used_by_another_product(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image_path' => 'products/shared-image.webp',
        ]);
        Product::factory()->create([
            'image_path' => 'products/shared-image.webp',
        ]);
        Storage::disk('public')->put('products/shared-image.webp', 'shared-image');

        $response = $this->put(route('products.update', $product), [
            ...$this->productData(),
            'sku' => $product->sku,
            'image' => UploadedFile::fake()->image('replacement.png', 400, 300),
        ]);

        $response->assertRedirect(route('products.show', $product));
        Storage::disk('public')->assertExists('products/shared-image.webp');
    }

    public function test_updating_without_a_new_image_preserves_the_existing_file(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image_path' => 'products/kept-image.webp',
        ]);
        Storage::disk('public')->put($product->image_path, 'existing-image');
        $data = $this->productData();
        unset($data['image']);

        $response = $this->put(route('products.update', $product), [
            ...$data,
            'sku' => $product->sku,
        ]);

        $product->refresh();

        $response->assertRedirect(route('products.show', $product));
        self::assertSame('products/kept-image.webp', $product->image_path);
        Storage::disk('public')->assertExists('products/kept-image.webp');
    }

    public function test_product_list_and_detail_render_the_saved_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'name' => 'Kopi Arabika',
            'image_path' => 'products/kopi.webp',
        ]);
        Storage::disk('public')->put($product->image_path, 'image-data');

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertSee('storage/products/kopi.webp');

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertSee('storage/products/kopi.webp')
            ->assertSee(route('products.invoice', $product))
            ->assertSee(route('products.invoice.download', $product));
    }

    /**
     * @return array{name: string, sku: string, price: string, stock: int, description: string}
     */
    private function productData(): array
    {
        return [
            'name' => 'Produk Uji',
            'sku' => 'SKU-PRODUK-UJI',
            'price' => '25000.00',
            'stock' => 5,
            'description' => 'Deskripsi produk uji.',
        ];
    }
}
