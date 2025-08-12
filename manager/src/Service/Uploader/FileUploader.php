<?php

declare(strict_types=1);

namespace App\Service\Uploader;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private FilesystemOperator $storage;
    private string $baseUrl;
    private string $localRoot;

    public function __construct(FilesystemOperator $storage, string $baseUrl, string $localRoot)
    {
        $this->storage = $storage;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->localRoot = rtrim($localRoot, '/');
    }

    /**
     * @throws FilesystemException
     */
    public function upload(UploadedFile $file): File
    {
        $path = date('Y/m/d');
        $name = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();

        if (!is_dir($this->localRoot . '/' . $path)) {
            $this->storage->createDirectory($path, ['visibility' => 'public']);
            @chmod($this->localRoot . '/' . $path, 0775);
        }

        $stream = fopen($file->getRealPath(), 'rb');
        $this->storage->writeStream(
            $path . '/' . $name,
            $stream,
            ['visibility' => 'public']
        );
        fclose($stream);

        return new File($path, $name, $file->getSize());
    }

    public function generateUrl(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }

    public function remove(string $path, string $name): void
    {
        $this->storage->delete($path . '/' . $name);
    }
}
