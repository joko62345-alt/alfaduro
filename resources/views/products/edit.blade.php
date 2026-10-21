@extends('products.layout')

@section('title', 'Edit Produk')

@section('content')
    <section class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Produk</h1>
        <p class="mt-2 text-sm text-gray-600">Unggah gambar baru hanya jika ingin mengganti gambar yang sekarang.</p>

        @include('products._form')
    </section>
@endsection
