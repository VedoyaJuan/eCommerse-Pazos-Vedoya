<?php

namespace App\Services;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    /**
     * Upload a file or URL to Cloudinary and return the secure URL.
     *
     * @param mixed $file File object (UploadedFile) or image URL string
     * @param string $folder Optional folder in Cloudinary
     * @return string|null Secure URL of the uploaded image
     */
    public function upload($file, string $folder = 'products'): ?string
    {
        if (!$file) {
            return null;
        }

        try {
            $upload = new UploadApi();
            
            // If it's a Laravel UploadedFile
            if ($file instanceof UploadedFile) {
                $source = $file->getRealPath();
            } else {
                // Otherwise it can be a local path or a remote URL string
                $source = $file;
            }

            $options = [
                'folder' => $folder,
                'resource_type' => 'auto',
            ];

            $response = $upload->upload($source, $options);
            
            return $response['secure_url'] ?? null;
        } catch (Exception $e) {
            Log::error('Cloudinary Upload Error: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => is_string($file) ? $file : 'UploadedFile instance'
            ]);
            return null;
        }
    }
}
