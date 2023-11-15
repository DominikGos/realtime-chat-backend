<?php 

namespace App\Traits;

use App\Http\Requests\File\FileDestroyRequest;
use App\Http\Requests\File\FileStoreRequest;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

Trait HasFile
{
    public FileService $fileService;
    private string $disk;
    private string $directory;

    public function initFileService(string $directory, string $disk = 'public'): void
    {
        $this->directory = $directory;
        $this->disk = $disk;
        $this->fileService = new FileService($directory, $disk);
    }

    public function storeFile(FileStoreRequest $request): JsonResponse
    {
        $filesLinks = [];

        foreach($request->file('files') as $file) {
            $filePath = $this->fileService->store($file);

            $filesLinks[] = Storage::disk($this->disk)->url($filePath);
        }

        return new JsonResponse([
            'files_links' => $filesLinks,
        ], 201);
    }

    public function destroyFile(FileDestroyRequest $request): JsonResponse
    {
        $this->fileService->destroy($request->file_link);

        return new JsonResponse(null, 204);
    }
}