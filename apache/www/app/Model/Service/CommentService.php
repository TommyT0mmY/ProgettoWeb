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
     * Gets all comments for a post.
     *
     * @param int $postId Post ID.
     * @return CommentDTO[] Comments with authors.
     */
    public function getCommentsByPostId(int $postId): array {
        return $this->commentRepository->findByPostId($postId);
    }

    /**
     * Creates a new comment.
     *
     * @param CreateCommentDTO $dto Comment data.
     * @return CommentDTO Created comment with author.
     * @throws ValidationException When user does not exist.
     * @throws ValidationException When user is suspended.
     * @throws ValidationException When comment text is empty.
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
     * Deletes a comment.
     *
     * Admins can delete any comment, users only their own.
     *
     * @param int $commentId Comment ID.
     * @param int $postId Post ID.
     * @param string $userId Requester user ID.
     * @param bool $isAdmin Whether requester is admin.
     * @throws ValidationException When comment does not exist.
     * @throws DomainException When user is not the comment owner and not admin.
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
