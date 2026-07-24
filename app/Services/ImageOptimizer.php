<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Optimize and convert an uploaded image to WebP.
     *
     * @param UploadedFile $file The uploaded file.
     * @param string $folder The destination storage folder.
     * @param int $maxWidth Maximum width of the image (null to bypass resizing).
     * @param int $quality WebP compression quality (1-100).
     * @return string Relative storage filepath of the saved WebP image.
     */
    public function convertToWebp(UploadedFile $file, string $folder = 'uploads', ?int $maxWidth = 1200, int $quality = 80): string
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
            default:
                // Unsupported, save as is
                return $file->store($folder, 'public');
        }

        if (!$image) {
            // Fallback: if GD fails, save the file original format
            return $file->store($folder, 'public');
        }

        // 2. Resize image if it exceeds maximum width
        $width = imagesx($image);
        $height = imagesy($image);

        if ($maxWidth && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $maxWidth);
            
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Handle transparency for PNG/WebP resizes
            if ($extension === 'png' || $extension === 'webp') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }
            
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resizedImage;
        }

        // 3. Generate a unique WebP filename
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid() . '.webp';
        
        // Ensure folder exists
        Storage::disk('public')->makeDirectory($folder);
        $absolutePath = Storage::disk('public')->path($folder . '/' . $filename);

        // 4. Save as WebP
        imagewebp($image, $absolutePath, $quality);
        imagedestroy($image);

        return $folder . '/' . $filename;
    }
}
