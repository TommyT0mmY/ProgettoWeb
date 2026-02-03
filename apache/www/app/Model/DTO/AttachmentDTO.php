<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class AttachmentDTO {
    public int $attachmentId;
    public int $postId;
    public string $fileName;
    public string $originalName;
    public string $mimeType;
    public int $fileSize;
    public string $createdAt;

    public function __construct(
        int $attachmentId,
        int $postId,
        string $fileName,
        string $originalName,
        string $mimeType,
        int $fileSize,
        string $createdAt
    ) {
        $this->attachmentId = $attachmentId;
        $this->postId = $postId;
        $this->fileName = $fileName;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->fileSize = $fileSize;
        $this->createdAt = $createdAt;
    }

    /**
     * Returns the URL to view/download the attachment
     */
    public function getUrl(): string {
        return '/api/attachments/' . $this->fileName;
    }

    /**
     * Returns human-readable file size
     */
    public function getFormattedSize(): string {
        if ($this->fileSize < 1024) {
            return $this->fileSize . ' B';
        } elseif ($this->fileSize < 1048576) {
            return round($this->fileSize / 1024, 1) . ' KB';
        } else {
            return round($this->fileSize / 1048576, 1) . ' MB';
        }
    }

    /**
     * Returns file extension from original name
     */
    public function getExtension(): string {
        return strtolower(pathinfo($this->originalName, PATHINFO_EXTENSION));
    }

    /**
     * Converts to array for JSON serialization
     */
    public function toArray(): array {
        return [
            'attachmentId' => $this->attachmentId,
            'postId' => $this->postId,
            'fileName' => $this->fileName,
            'originalName' => $this->originalName,
            'mimeType' => $this->mimeType,
            'fileSize' => $this->fileSize,
            'formattedSize' => $this->getFormattedSize(),
            'extension' => $this->getExtension(),
            'url' => $this->getUrl(),
            'createdAt' => $this->createdAt
        ];
    }
}
