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
}