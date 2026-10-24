<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    #[DataProvider('supportedImageUploads')]
    public function test_uploading_a_supported_image_saves_a_manipulated_webp_image(string $filename): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image($filename, 640, 480);
        $imageWithoutWatermark = (string) Image::decodeSplFileInfo($photo)
            ->cover(300, 300)
            ->rotate(90)
            ->encodeUsingFileExtension('webp', quality: 85);

        $response = $this->post(route('image.upload'), [
            'photo' => $photo,
            'rotation' => '90',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Gambar berhasil diupload dan dimanipulasi!')
            ->assertSessionHas('image_url');

        $uploadedFiles = Storage::disk('public')->allFiles('uploads');
        self::assertCount(1, $uploadedFiles);
        self::assertMatchesRegularExpression('/^uploads\/[0-9a-f-]{36}\.webp$/', $uploadedFiles[0]);
        Storage::disk('public')->assertExists($uploadedFiles[0]);
        self::assertNotSame(
            $imageWithoutWatermark,
            Storage::disk('public')->get($uploadedFiles[0])
        );

        $imageSize = getimagesize(Storage::disk('public')->path($uploadedFiles[0]));
        self::assertIsArray($imageSize);
        self::assertSame('image/webp', $imageSize['mime']);
        self::assertSame(300, $imageSize[0]);
        self::assertSame(300, $imageSize[1]);

        $this->get(route('image.index'))
            ->assertSee('Hasil manipulasi')
            ->assertSee(Storage::disk('public')->url($uploadedFiles[0]));
    }

    public static function supportedImageUploads(): array
    {
        return [
            'JPEG image' => ['photo.jpg'],
            'PNG image' => ['photo.png'],
        ];
    }

    public function test_uploading_a_non_image_file_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->from(route('image.index'))
            ->post(route('image.upload'), [
                'photo' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
                'rotation' => '90',
            ]);

        $response->assertRedirect(route('image.index'))
            ->assertSessionHasErrors('photo');
        self::assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_uploading_an_image_larger_than_two_megabytes_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->from(route('image.index'))
            ->post(route('image.upload'), [
                'photo' => UploadedFile::fake()->image('large.jpg', 10, 10)->size(2049),
                'rotation' => '90',
            ]);

        $response->assertRedirect(route('image.index'))
            ->assertSessionHasErrors('photo');
        self::assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_rotation_outside_the_supported_angles_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->from(route('image.index'))
            ->post(route('image.upload'), [
                'photo' => UploadedFile::fake()->image('photo.jpg', 640, 480),
                'rotation' => '45',
            ]);

        $response->assertRedirect(route('image.index'))
            ->assertSessionHasErrors('rotation');
        self::assertSame([], Storage::disk('public')->allFiles());
    }
}
