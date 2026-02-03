<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Model\DTO\AttachmentDTO;
use Unibostu\Model\Repository\AttachmentRepository;

class AttachmentService {
    private AttachmentRepository $attachmentRepository;
    
    // Configuration constants
    private const UPLOAD_DIR = '/var/uploads/';
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
    private const MAX_FILES_PER_POST = 5;
    private const MAX_ORIGINAL_NAME_LENGTH = 255; // Max length for original filename in DB
    private const MAX_MIME_TYPE_LENGTH = 100; // Max length for MIME type in DB
    private const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/zip',
        'application/x-rar-compressed',
        'application/octet-stream'
    ];

    public function __construct() {
        $this->attachmentRepository = new AttachmentRepository();
    }

    /**
     * Process and save uploaded files for a post
     * 
     * @param int $postId The post ID to attach files to
     * @param array $files The $_FILES array for the upload field
     * @return AttachmentDTO[] Array of saved attachment DTOs
     * @throws ValidationException if validation fails
     */
    public function processUploadedFiles(int $postId, array $files): array {
        $savedAttachments = [];
        // Normalize files array (handle both single and multiple uploads)
        $normalizedFiles = $this->normalizeFilesArray($files);
        // Validate file count
        if (count($normalizedFiles) > self::MAX_FILES_PER_POST) {
            throw ValidationException::build()
                ->addError(ValidationErrorCode::FILE_MAX_COUNT_EXCEEDED)
                ->throwIfAny();
        }
        foreach ($normalizedFiles as $file) {
            if ($file['error'] === UPLOAD_ERR_NO_FILE) { // Skip empty uploads
                continue;
            }
            // Validate each file
            $this->validateFile($file);
            // Generate safe filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $safeFileName = $this->generateSafeFileName($extension);
            // Move file to upload directory
            $destination = self::UPLOAD_DIR . $safeFileName;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw ValidationException::build()
                    ->addError(ValidationErrorCode::FILE_UPLOAD_ERROR)
                    ->throwIfAny();
            }
            // Get actual MIME type using finfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $destination);
            // Truncate MIME type if too long (shouldn't happen with standard types)
            if (strlen($mimeType) > self::MAX_MIME_TYPE_LENGTH) {
                $mimeType = substr($mimeType, 0, self::MAX_MIME_TYPE_LENGTH);
            }
            // Save to database
            $attachmentId = $this->attachmentRepository->save(
                postId: $postId,
                fileName: $safeFileName,
                originalName: $file['name'],
                mimeType: $mimeType,
                fileSize: $file['size']
            );
            $savedAttachments[] = $this->attachmentRepository->findById($attachmentId);
        }
        return $savedAttachments;
    }

    /**
     * Get attachment by filename for serving
     */
    public function getAttachmentByFileName(string $fileName): ?AttachmentDTO {
        return $this->attachmentRepository->findByFileName($fileName);
    }

    /**
     * Get all attachments for a post
     * @return AttachmentDTO[]
     */
    public function getAttachmentsByPostId(int $postId): array {
        return $this->attachmentRepository->findByPostId($postId);
    }

    /**
     * Get the file path for serving
     */
    public function getFilePath(string $fileName): string {
        return self::UPLOAD_DIR . $fileName;
    }

    /**
     * Check if file exists on disk
     */
    public function fileExists(string $fileName): bool {
        $path = self::UPLOAD_DIR . $fileName;
        return file_exists($path) && is_file($path);
    }

    /**
     * Delete attachment and its file
     */
    public function deleteAttachment(int $attachmentId): bool {
        $attachment = $this->attachmentRepository->findById($attachmentId);
        if (!$attachment) {
            return false;
        }
        
        // Delete file from disk
        $filePath = self::UPLOAD_DIR . $attachment->fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        return $this->attachmentRepository->deleteById($attachmentId);
    }

    /**
     * Delete all attachments for a post
     */
    public function deleteAttachmentsByPostId(int $postId): void {
        $attachments = $this->attachmentRepository->findByPostId($postId);
        
        // Delete files from disk
        foreach ($attachments as $attachment) {
            $filePath = self::UPLOAD_DIR . $attachment->fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // Delete from database
        $this->attachmentRepository->deleteByPostId($postId);
    }

    /**
     * Normalize $_FILES array to handle both single and multiple uploads
     */
    private function normalizeFilesArray(array $files): array {
        $normalized = [];
        
        // Check if it's a multiple file upload
        if (is_array($files['name'])) {
            $fileCount = count($files['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                $normalized[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
            }
        } else {
            $normalized[] = $files;
        }
        
        return $normalized;
    }

    /**
     * Validate a single file
     */
    private function validateFile(array $file): void {
        $validExcBuilder = ValidationException::build();
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
                $validExcBuilder->addError(ValidationErrorCode::FILE_TOO_LARGE);
            } else {
                $validExcBuilder->addError(ValidationErrorCode::FILE_UPLOAD_ERROR);
            }
            $validExcBuilder->throwIfAny();
        }
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $validExcBuilder->addError(ValidationErrorCode::FILE_TOO_LARGE);
            $validExcBuilder->throwIfAny();
        }
        // Check filename length
        if (strlen($file['name']) > self::MAX_ORIGINAL_NAME_LENGTH) {
            $validExcBuilder->addError(ValidationErrorCode::FILE_NAME_TOO_LONG);
            $validExcBuilder->throwIfAny();
        }
        // Check extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $validExcBuilder->addError(ValidationErrorCode::FILE_TYPE_NOT_ALLOWED);
            $validExcBuilder->throwIfAny();
        }
        // Check MIME type from temp file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            $validExcBuilder->addError(ValidationErrorCode::FILE_TYPE_NOT_ALLOWED);
            $validExcBuilder->throwIfAny();
        }
    }

    /**
     * Generate a safe, unique filename maintaining the original extension
     * 
     * @param string $extension The file extension
     * @return string The generated filename
     */
    private function generateSafeFileName(string $extension): string {
        return time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }

    /**
     * Get max file size in bytes
     */
    public static function getMaxFileSize(): int {
        return self::MAX_FILE_SIZE;
    }

    /**
     * Get max files per post
     */
    public static function getMaxFilesPerPost(): int {
        return self::MAX_FILES_PER_POST;
    }

    /**
     * Get allowed extensions
     */
    public static function getAllowedExtensions(): array {
        return self::ALLOWED_EXTENSIONS;
    }
}
