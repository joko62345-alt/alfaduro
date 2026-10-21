@extends('products.layout')

@section('title', 'Tambah Produk')

@section('content')
    <section class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Produk</h1>
        <p class="mt-2 text-sm text-gray-600">Lengkapi data produk dan unggah gambar JPEG, PNG, atau WebP (maksimal 2 MB).</p>

        @include('products._form')
    </section>
@endsection
