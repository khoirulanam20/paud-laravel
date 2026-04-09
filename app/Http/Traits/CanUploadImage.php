<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait CanUploadImage
{
    /**
     * Upload and compress image using GD
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param int $quality
     * @return string
     */
    public function uploadImage($file, $path, $quality = 70)
    {
        // Generate unique filename with .jpg extension for uniform compression
        $filename = Str::random(40) . '.jpg';
        $fullPath = $path . '/' . $filename;
        
        $imagePath = $file->getRealPath();
        
        // Get MIME type
        $info = getimagesize($imagePath);
        $mime = $info['mime'] ?? $file->getClientMimeType();

        $image = null;

        // Load image resource based on mime type
        try {
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($imagePath);
                    // Handle PNG transparency by filling with white background
                    if ($image) {
                        $width = imagesx($image);
                        $height = imagesy($image);
                        $background = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($background, 255, 255, 255);
                        imagefill($background, 0, 0, $white);
                        imagecopy($background, $image, 0, 0, 0, 0, $width, $height);
                        imagedestroy($image);
                        $image = $background;
                    }
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $image = imagecreatefromwebp($imagePath);
                    }
                    break;
            }
        } catch (\Exception $e) {
            $image = null;
        }

        if (!$image) {
            // Fallback for unsupported formats or errors
            return $file->store($path, 'public');
        }

        // Capture compressed JPEG output
        ob_start();
        imagejpeg($image, null, $quality);
        $content = ob_get_clean();
        
        imagedestroy($image);

        // Store to public disk
        Storage::disk('public')->put($fullPath, $content);

        return $fullPath;
    }
}
