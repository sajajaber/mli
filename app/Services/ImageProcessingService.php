<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        bool $preserveTransparency = false
    ): string {
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

    protected function sourceHasAlpha(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), ['image/png', 'image/webp']);
    }
}
