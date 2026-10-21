@extends('products.layout')

@section('title', $product->name)

@section('content')
    <article class="overflow-hidden rounded-xl bg-white shadow-sm">
        @if ($product->image_path)
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="max-h-[32rem] w-full object-contain bg-gray-50">
        @else
            <div class="flex h-64 items-center justify-center bg-gray-100 text-sm text-gray-500">Belum ada gambar</div>
        @endif

        <div class="space-y-5 p-6 sm:p-8">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                    <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $product->name }}</h1>
                </div>
                <p class="text-xl font-semibold text-gray-900">Rp {{ number_format((float) $product->price, 2, ',', '.') }}</p>
            </div>

            <p class="text-sm text-gray-600">Stok: {{ $product->stock }}</p>
            @if ($product->description)
                <p class="whitespace-pre-line text-gray-700">{{ $product->description }}</p>
            @endif

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('products.edit', $product) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Edit Produk</a>
                <a href="{{ route('products.invoice', $product) }}" target="_blank" rel="noopener" class="rounded-md border border-indigo-300 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Lihat Rincian Harga PDF</a>
                <a href="{{ route('products.invoice.download', $product) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Unduh Rincian Harga PDF</a>
                <a href="{{ route('products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali ke Daftar</a>
            </div>
        </div>
    </article>
@endsection
