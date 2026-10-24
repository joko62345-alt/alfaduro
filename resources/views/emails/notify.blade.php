<div>
    <!-- Smile, breathe, and go slowly. - Thich Nhat Hanh -->
</div>
<x-mail::message>
# Halo {{ $user->name }}

Terima kasih telah bergabung di {{ config('app.name') }}.

Akun Anda berhasil dibuat. Silakan verifikasi alamat email Anda sebelum mengakses dashboard.

<x-mail::button :url="route('dashboard', absolute: true)">
Lihat Dashboard
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
