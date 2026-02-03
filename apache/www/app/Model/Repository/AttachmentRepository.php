<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\AttachmentDTO;
use PDO;

class AttachmentRepository extends BaseRepository {

    /**
     * Save a new attachment record
     */
    public function save(int $postId, string $fileName, string $originalName, string $mimeType, int $fileSize): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO post_attachments (post_id, file_name, original_name, mime_type, file_size, created_at)
             VALUES (:postId, :fileName, :originalName, :mimeType, :fileSize, NOW())"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':fileName', $fileName, PDO::PARAM_STR);
        $stmt->bindValue(':originalName', $originalName, PDO::PARAM_STR);
        $stmt->bindValue(':mimeType', $mimeType, PDO::PARAM_STR);
        $stmt->bindValue(':fileSize', $fileSize, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Find attachment by file name
     */
    public function findByFileName(string $fileName): ?AttachmentDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM post_attachments WHERE file_name = :fileName"
        );
        $stmt->bindValue(':fileName', $fileName, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->rowToDTO($row);
    }

    /**
     * Find attachment by ID
     */
    public function findById(int $attachmentId): ?AttachmentDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM post_attachments WHERE attachment_id = :attachmentId"
        );
        $stmt->bindValue(':attachmentId', $attachmentId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->rowToDTO($row);
    }

    /**
     * Find all attachments for a post
     * @return AttachmentDTO[]
     */
    public function findByPostId(int $postId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM post_attachments WHERE post_id = :postId ORDER BY created_at ASC"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $attachments = [];
        foreach ($rows as $row) {
            $attachments[] = $this->rowToDTO($row);
        }
        
        return $attachments;
    }

    /**
     * Delete attachment by ID
     */
    public function deleteById(int $attachmentId): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM post_attachments WHERE attachment_id = :attachmentId"
        );
        $stmt->bindValue(':attachmentId', $attachmentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete all attachments for a post
     */
    public function deleteByPostId(int $postId): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM post_attachments WHERE post_id = :postId"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Convert database row to DTO
     */
    protected function rowToDTO(array $row): AttachmentDTO {
        return new AttachmentDTO(
            attachmentId: (int)$row['attachment_id'],
            postId: (int)$row['post_id'],
            fileName: $row['file_name'],
            originalName: $row['original_name'],
            mimeType: $row['mime_type'],
            fileSize: (int)$row['file_size'],
            createdAt: $row['created_at']
        );
    }
}
