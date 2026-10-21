<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', [
            'products' => Product::query()->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        unset($validated['image']);

        $imagePath = $this->storeImage($request->file('image'));

        try {
            $product = DB::transaction(fn (): Product => Product::create([
                ...$validated,
                'image_path' => $imagePath,
            ]));
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->rules($product));
        unset($validated['image']);

        $oldImagePath = $product->image_path;
        $newImagePath = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : null;

        try {
            DB::transaction(function () use ($product, $validated, $newImagePath): void {
                $product->fill($validated);

                if ($newImagePath !== null) {
                    $product->image_path = $newImagePath;
                }

                $product->save();
            });
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                $this->deleteImage($newImagePath);
            }

            throw $exception;
        }

        if (
            $newImagePath !== null
            && $oldImagePath !== null
            && $oldImagePath !== $newImagePath
            && ! Product::query()
                ->where('image_path', $oldImagePath)
                ->where($product->getKeyName(), '<>', $product->getKey())
                ->exists()
        ) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?Product $product = null): array
    {
        $skuRule = Rule::unique('products', 'sku');

        if ($product !== null) {
            $skuRule->ignore($product);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', $skuRule],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image' => [
                $product === null ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    private function storeImage(UploadedFile $file): string
    {
        $image = (new ImageManager(new Driver))
            ->decodePath($file->getPathname())
            ->orient()
            ->scaleDown(width: 1200, height: 1200);

        $imagePath = 'products/'.Str::uuid().'.webp';
        $encodedImage = (string) $image->encodeUsingFormat(Format::WEBP, quality: 80);

        if (! Storage::disk('public')->put($imagePath, $encodedImage)) {
            throw new RuntimeException('Gambar produk tidak dapat disimpan ke storage.');
        }

        return $imagePath;
    }

    private function deleteImage(string $imagePath): void
    {
        if (! Storage::disk('public')->delete($imagePath)) {
            Log::warning('Gagal menghapus file gambar produk.', [
                'image_path' => $imagePath,
            ]);
        }
    }
}
