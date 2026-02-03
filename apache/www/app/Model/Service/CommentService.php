<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CommentRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\CreateCommentDTO;
use Unibostu\Model\DTO\CommentDTO;
use Unibostu\Core\exceptions\DomainException;
use Unibostu\Core\exceptions\DomainErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\exceptions\ValidationErrorCode;

class CommentService {
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;

    public function __construct() {
        $this->commentRepository = new CommentRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Gets all comments for a post with authors
     * 
     * @param int $postId Post ID
     * @return CommentWithAuthorDTO[] Array of comments with authors
     */
    public function getCommentsByPostId(int $postId): array {
        return $this->commentRepository->findByPostId($postId);
    }

    /**
     * Creates a new comment
     * 
     * @throws ValidationException if userId is invalid or doesn't exist
     * @throws ValidationException if comment text is empty
     * @throws ValidationException if user is suspended
     * @return CommentWithAuthorDTO The created comment with author
     */
    public function createComment(CreateCommentDTO $dto): CommentDTO {
        // Verify user exists
        $user = $this->userRepository->findByUserId($dto->userId);
        if (!$user) {
            ValidationException::build()
                ->addError(ValidationErrorCode::USER_NOT_FOUND)
                ->throwIfAny();
        }
        if ($user->suspended) {
            ValidationException::build()
                ->addError(ValidationErrorCode::USER_SUSPENDED)
                ->throwIfAny();
        }
        if (empty($dto->text)) {
            ValidationException::build()
                ->addError(ValidationErrorCode::COMMENT_TEXT_REQUIRED)
                ->throwIfAny();
        }
        return $this->commentRepository->save($dto);
    }

    /**
     * Deletes a comment
     * 
     * @param int $commentId Comment ID
     * @param int $postId Post ID
     * @param string $userId User ID of the requester
     * @param bool $isAdmin Whether the requester is an admin
     * @throws ValidationException if userId doesn't exist or comment doesn't exist
     * @throws DomainException if user is not owner of comment (and not admin)
     */
    public function deleteComment(int $commentId, int $postId, string $userId, bool $isAdmin = false): void {
        $comment = $this->commentRepository->findById($commentId, $postId);
        if (!$comment) {
            ValidationException::build()
                ->addError(ValidationErrorCode::COMMENT_NOT_FOUND)
                ->throwIfAny();
        }
        // Admin can delete any comment, users can only delete their own
        if (!$isAdmin && $comment->author->userId !== $userId) {
            throw new DomainException(DomainErrorCode::NOT_COMMENT_OWNER);
        }
        $this->commentRepository->delete($commentId, $postId);
    }
}
