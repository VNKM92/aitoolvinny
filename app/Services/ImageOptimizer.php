<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Optimize and convert an uploaded image to WebP.
     * Keeps backwards compatibility.
     */
    public function convertToWebp(UploadedFile $file, string $folder = 'uploads', ?int $maxWidth = 1200, int $quality = 80): string
    {
        return $this->optimizeAndConvert($file, 'webp', $folder, $maxWidth, $quality);
    }

    /**
     * Optimize and convert an uploaded image to AVIF.
     */
    public function convertToAvif(UploadedFile $file, string $folder = 'uploads', ?int $maxWidth = 1200, int $quality = 80): string
    {
        return $this->optimizeAndConvert($file, 'avif', $folder, $maxWidth, $quality);
    }

    /**
     * Optimize and convert an uploaded image to WebP or AVIF.
     *
     * @param UploadedFile $file The uploaded file.
     * @param string $format The target format ('webp' or 'avif').
     * @param string $folder The destination storage folder.
     * @param int $maxWidth Maximum width of the image (null to bypass resizing).
     * @param int $quality Compression quality (1-100).
     * @return string Relative storage filepath of the saved optimized image.
     */
    public function optimizeAndConvert(UploadedFile $file, string $format = 'webp', string $folder = 'uploads', ?int $maxWidth = 1200, int $quality = 80): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        // 1. Create image resource based on extension
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = @imagecreatefromjpeg($path);
                break;
            case 'png':
                $image = @imagecreatefrompng($path);
                // Keep transparency
                if ($image) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                break;
            case 'gif':
                $image = @imagecreatefromgif($path);
                break;
            case 'webp':
                $image = @imagecreatefromwebp($path);
                break;
            case 'avif':
                if (function_exists('imagecreatefromavif')) {
                    $image = @imagecreatefromavif($path);
                } else {
                    $image = null;
                }
                break;
            default:
                // Unsupported, save as is
                return $file->store($folder, 'public');
        }

        if (!$image) {
            // Fallback: if GD fails, save the file in original format
            return $file->store($folder, 'public');
        }

        // 2. Resize image if it exceeds maximum width
        $width = imagesx($image);
        $height = imagesy($image);

        if ($maxWidth && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $maxWidth);
            
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Handle transparency for PNG/WebP/AVIF resizes
            if (in_array($extension, ['png', 'webp', 'avif'])) {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }
            
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resizedImage;
        }

        // 3. Generate a unique filename with target format extension
        $targetFormat = strtolower($format);
        if ($targetFormat === 'avif' && !function_exists('imageavif')) {
            // Fallback to webp if AVIF is not supported in GD
            $targetFormat = 'webp';
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid() . '.' . $targetFormat;
        
        // Ensure folder exists
        Storage::disk('public')->makeDirectory($folder);
        $absolutePath = Storage::disk('public')->path($folder . '/' . $filename);

        // 4. Save in optimized format
        if ($targetFormat === 'avif' && function_exists('imageavif')) {
            imageavif($image, $absolutePath, $quality);
        } else {
            imagewebp($image, $absolutePath, $quality);
        }
        
        imagedestroy($image);

        return $folder . '/' . $filename;
    }
}
