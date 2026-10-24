<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;
use RuntimeException;

class ImageController extends Controller
{
    public function index(): View
    {
        return view('upload');
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'rotation' => ['required', 'integer', 'in:0,90,180,270'],
        ]);

        $image = Image::decodeSplFileInfo($request->file('photo'))
            ->cover(300, 300)
            ->rotate((float) $validated['rotation'])
            ->text('JAYA SELALU AREMA KU', 10, 285, function (FontFactory $font): void {
                $font->size(5)
                    ->color('#ffffff')
                    ->stroke('#000000', 1);
            });

        $imagePath = 'uploads/'.Str::uuid().'.webp';
        $encodedImage = (string) $image->encodeUsingFileExtension('webp', quality: 85);

        if (! Storage::disk('public')->put($imagePath, $encodedImage)) {
            throw new RuntimeException('Gambar hasil manipulasi tidak dapat disimpan.');
        }

        return back()->with(
            [
                'success' => 'Gambar berhasil diupload dan dimanipulasi!',
                'image_url' => Storage::disk('public')->url($imagePath),
            ]
        );
    }
}
