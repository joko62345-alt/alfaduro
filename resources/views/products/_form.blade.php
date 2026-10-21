<form action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
    @csrf
    @if ($product->exists)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama produk</label>
        <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" maxlength="255" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" maxlength="100" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
            <input id="price" name="price" type="number" value="{{ old('price', $product->price) }}" min="0" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
            <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" min="0" step="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi (opsional)</label>
        <textarea id="description" name="description" rows="4" maxlength="10000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @if ($product->exists && $product->image_path)
        <div>
            <p class="mb-2 text-sm font-medium text-gray-700">Gambar saat ini</p>
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-40 w-40 rounded-md object-cover">
        </div>
    @endif

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">{{ $product->exists ? 'Ganti gambar (opsional)' : 'Gambar produk' }}</label>
        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @required(!$product->exists) class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="mt-1 text-xs text-gray-500">JPEG, PNG, atau WebP; maksimal 2 MB. Gambar akan diarahkan sesuai orientasi EXIF, diperkecil maksimal 1200 × 1200 px, dan disimpan sebagai WebP.</p>
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            {{ $product->exists ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
        <a href="{{ $product->exists ? route('products.show', $product) : route('products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
    </div>
</form>
