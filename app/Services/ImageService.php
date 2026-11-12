<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Validates, resizes and converts uploads to WebP (GD). Originals are never kept.
 * Stores a main image (max 1600px wide) plus 480 and 960 wide variants on the public disk.
 */
class ImageService
{
    public const MAX_BYTES = 8 * 1024 * 1024;
    public const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
    public const WIDTHS = [480, 960, 1600];

    /** @return array{path:string,width:int,height:int,variants:array<int,string>} */
    public function store(UploadedFile $file, string $dir = 'uploads'): array
    {
        if (! $file->isValid() || $file->getSize() > self::MAX_BYTES) {
            throw new InvalidArgumentException('Image is invalid or larger than 8 MB.');
        }
        // Sniff the real type; never trust the extension or client MIME.
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        if (! in_array($mime, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Only JPG, PNG or WebP images are allowed.');
        }

        $src = imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (! $src) {
            throw new InvalidArgumentException('Could not read that image.');
        }
        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

        $w = imagesx($src);
        $h = imagesy($src);
        $base = $dir.'/'.now()->format('Y/m').'/'.Str::lower(Str::random(16));
        $variants = [];

        foreach (self::WIDTHS as $width) {
            $targetW = min($width, $w);
            if ($width !== self::WIDTHS[array_key_last(self::WIDTHS)] && $w <= $width) {
                continue; // do not upscale small images into extra variants
            }
            $targetH = (int) round($h * ($targetW / $w));
            $dst = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $w, $h);

            ob_start();
            imagewebp($dst, null, 82);
            $bytes = ob_get_clean();
            $path = "{$base}-{$targetW}.webp";
            Storage::disk('public')->put($path, $bytes);
            $variants[$targetW] = $path;
            imagedestroy($dst);
        }
        imagedestroy($src);

        $main = $variants[array_key_last($variants)];

        return ['path' => $main, 'width' => min($w, 1600), 'height' => (int) round($h * (min($w, 1600) / $w)), 'variants' => $variants];
    }

    /** Store and register in the media library. */
    public function storeMedia(UploadedFile $file, string $alt): Media
    {
        $r = $this->store($file);

        return Media::create(['path' => $r['path'], 'alt' => $alt, 'width' => $r['width'], 'height' => $r['height'], 'variants' => $r['variants']]);
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
