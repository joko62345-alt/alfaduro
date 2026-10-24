<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Terima kasih telah mendaftar. Verifikasi alamat email Anda melalui tautan yang kami kirim sebelum menggunakan dashboard.') }}
    </div>

    @if (session('welcome_email_status') === 'queued' && config('mail.default') === 'log')
        <div class="mb-4 rounded-md bg-blue-50 p-3 text-sm text-blue-800">
            Email selamat datang masuk antrean dan akan diproses beberapa detik lagi. Jalankan <code>php artisan queue:work</code>. Mailer <code>log</code> hanya mencatat email di <code>storage/logs/laravel.log</code>, bukan mengirim ke inbox.
        </div>
    @elseif (session('welcome_email_status') === 'queued')
        <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">
            Email selamat datang masuk antrean dan akan dikirim beberapa detik lagi. Pastikan worker <code>php artisan queue:work</code> sedang berjalan.
        </div>
    @elseif (session('welcome_email_status') === 'failed')
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-800" role="alert">
            Email selamat datang gagal dikirim. Periksa log aplikasi untuk mengetahui penyebabnya.
        </div>
    @endif

    @if (session('verification_email_status') === 'logged')
        <div class="mb-4 rounded-md bg-blue-50 p-3 text-sm text-blue-800">
            Tautan verifikasi dicatat di log aplikasi, bukan dikirim ke inbox. Periksa <code>storage/logs/laravel.log</code>.
        </div>
    @elseif (session('verification_email_status') === 'sent')
        <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">
            Tautan verifikasi telah diteruskan ke server email tujuan. Periksa inbox atau folder spam.
        </div>
    @elseif (session('verification_email_status') === 'failed')
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-800" role="alert">
            Tautan verifikasi gagal dikirim. Periksa log aplikasi, lalu coba kirim ulang.
        </div>
    @endif

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Permintaan pengiriman tautan verifikasi telah diproses.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
