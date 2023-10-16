<?php 

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService 
{
    private string $directory;
    private string $disk;

    public function __construct(string $directory, string $disk)
    {
        $this->directory = $directory;
        $this->disk = $disk;
    }

    public function store(UploadedFile $file): string
    {
        $path = $file->store($this->directory, $this->disk);

        return $path;
    }
    
    public function destroy(string $fileLink): void
    {
        $filePath = $this->getFilePath($fileLink);

        Storage::disk($this->disk)->delete($filePath);
    }

    public function getFilePath(string $linkToFile): string
    {
        $filePath = explode('storage/', $linkToFile);
        $filePath = end($filePath);

        return $filePath;
    }
}