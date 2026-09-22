<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class ImageProcessingService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(Driver::class);
    }

    /**
     * Resize (if needed) and compress an uploaded image, then store it on the public disk.
     * Returns the stored relative path (e.g. "shows/uuid.jpg").
     */
    public function processAndStore(
        UploadedFile $file,
        string $directory,
        int $maxWidth,
        int $quality = 85,
        bool $preserveTransparency = false,
        ?string $field = null,
    ): string {
        $this->rejectDuplicateImage($file, $field);

        $image = $this->manager->decode($file->getRealPath());

        // Never upscale — only shrink if the original is wider than allowed.
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        $keepPng = $preserveTransparency && $this->sourceHasAlpha($file);

        $filename = $directory . '/' . Str::uuid() . ($keepPng ? '.png' : '.jpg');

        $encoded = $keepPng
            ? $image->encodeUsingFormat(Format::PNG)
            : $image->encodeUsingFormat(Format::JPEG, quality: $quality);

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    protected function rejectDuplicateImage(UploadedFile $file, ?string $field = null): void
    {
        $fingerprint = $this->imageFingerprint($file->getRealPath());
        $disk = Storage::disk('public');

        foreach (['shows', 'news', 'people', 'clients'] as $directory) {
            foreach ($disk->allFiles($directory) as $path) {
                try {
                    if ($fingerprint === $this->imageFingerprint($disk->get($path))) {
                        throw ValidationException::withMessages([
                            $field ?? 'image' => 'This image is already being used. Please choose a different image.',
                        ]);
                    }
                } catch (ValidationException $exception) {
                    throw $exception;
                } catch (\Throwable) {
                    // Ignore files that cannot be decoded as images.
                }
            }
        }
    }

    protected function imageFingerprint(string $source): string
    {
        $image = $this->manager->decode($source);

        // Normalize the image so the same image is detected even when its
        // original file format/compression differs.
        $image->resize(64, 64);

        $normalized = $image->encodeUsingFormat(Format::PNG);

        return hash('sha256', (string) $normalized);
    }

    protected function sourceHasAlpha(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), ['image/png', 'image/webp']);
    }
}
