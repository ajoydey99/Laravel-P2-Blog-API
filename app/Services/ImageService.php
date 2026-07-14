<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    // hash image name and store
    public function storeImage(UploadedFile $file)
    {
        $imageName = $file->hashName();

        return $file->storeAs('posts', $imageName, 'public');
    }

    // delete image from storage
    public function deleteImage(String $filePath)
    {
        Storage::disk('public')->delete($filePath);
    }
}
