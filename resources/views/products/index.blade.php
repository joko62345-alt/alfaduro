@extends('products.layout')

@section('title', 'Produk')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Produk</h1>
            <p class="mt-1 text-sm text-gray-600">Daftar produk dan gambar yang sudah diproses.</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Tambah Produk
        </a>
    </div>

    @if ($products->isEmpty())
        <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-600">
            Belum ada produk.
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <article class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        @if ($product->image_path)
                            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-56 w-full object-cover">
                        @else
                            <div class="flex h-56 items-center justify-center bg-gray-100 text-sm text-gray-500">Belum ada gambar</div>
                        @endif
                    </a>
                    <div class="space-y-2 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold text-gray-900">{{ $product->name }}</h2>
                            <span class="text-sm font-medium text-gray-700">Rp {{ number_format((float) $product->price, 2, ',', '.') }}</span>
                        </div>
                        <p class="text-sm text-gray-500">SKU: {{ $product->sku }} · Stok: {{ $product->stock }}</p>
                        <a href="{{ route('products.show', $product) }}" class="inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
@endsection
