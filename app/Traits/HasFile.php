<?php 

namespace App\Traits;

use App\Http\Requests\File\FileDestroyRequest;
use App\Http\Requests\File\FileStoreRequest;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;

Trait HasFile
{
    public FileService $fileService;

    public function initFileService(string $directory, string $disk = 'public'): void
    {
        $this->fileService = new FileService($directory, $disk);
    }

    public function storeFile(FileStoreRequest $request): JsonResponse
    {
        $filesLinks = [];

        foreach($request->file('files') as $file) {
            $filePath = $this->fileService->store($file);
            $filesLinks[] = asset($filePath);
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