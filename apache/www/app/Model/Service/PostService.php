<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\PostRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Model\DTO\CreatePostDTO;
use Unibostu\Model\DTO\PostDTO;
use Unibostu\Core\exceptions\DomainException;
use Unibostu\Core\exceptions\DomainErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\exceptions\ValidationErrorCode;

class PostService {
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;

    public function __construct() {
        $this->postRepository = new PostRepository();
        $this->userRepository = new UserRepository();
        $this->courseRepository = new CourseRepository();
    }

    /**
     * Gets posts based on specified filters
     * 
     * @param PostQuery $postQuery Query with filters
     * @return PostDTO[] Array of PostDTO matching the filters
     */
    public function getPosts(PostQuery $postQuery): array {
        return $this->postRepository->findWithFilters($postQuery);
    }

    /**
     * Gets a single post with details
     * @param int $postId Post ID
     * @param string|null $userId Current user ID to populate likedByUser
     * @return PostDTO|null Post details or null if not found
     */
    public function getPostDetails(int $postId, ?string $userId = null): ?PostDTO {
        return $this->postRepository->findById($postId, $userId);
    }

    /**
     * Creates a new post for a user
     * Users can only post to ONE SINGLE course
     * Tags must belong to the same course
     * Categories are optional
     *
     * @throws ValidationException if userId is invalid or course doesn't belong to user
     * @return int The created post ID
     */
    public function createPost(CreatePostDTO $dto): int {
        $exceptionBuilder = ValidationException::build();
        // Resolve userId to user
        $user = $this->userRepository->findByUserId($dto->userId);
        if (!$user) {
            $exceptionBuilder->addError(ValidationErrorCode::USER_NOT_FOUND);
        }
        // Verify user is enrolled in the course
        if (!$this->courseRepository->isUserEnrolled($dto->userId, $dto->courseId)) {
            $exceptionBuilder->addError(ValidationErrorCode::USER_NOT_ENROLLED);
        }
        // Verify all tags belong to the selected course
        foreach ($dto->tags as $tag) {
            if (!isset($tag['tagId']) || !isset($tag['courseId'])) {
                $exceptionBuilder->addError(ValidationErrorCode::TAG_REQUIRED);
                break;
            }
            if ($tag['courseId'] !== $dto->courseId) {
                $exceptionBuilder->addError(ValidationErrorCode::TAG_MISMATCH);
                break;
            }
        }
        if (empty($dto->title)) {
            $exceptionBuilder->addError(ValidationErrorCode::TITLE_REQUIRED);
        }
        if (empty($dto->description)) {
            $exceptionBuilder->addError(ValidationErrorCode::DESCRIPTION_REQUIRED);
        }
        
        $exceptionBuilder->throwIfAny();
        return $this->postRepository->save($dto);
    }

    /**
     * Reaction (like/dislike) to a post
     * 
     * @param int $postId Post ID
     * @param string $userId User ID
     * @param string $reaction "like", "dislike" or "remove"
     */
    public function setReaction(int $postId, string $userId, string $reaction): void {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            ValidationException::build()
                ->addError(ValidationErrorCode::POST_NOT_FOUND)
                ->throwIfAny();
        }
        if ($reaction === 'remove') {
            $this->postRepository->removeReaction($postId, $userId);
        } elseif ($reaction === 'like') {
            $this->postRepository->setReaction($postId, $userId, true);
        } elseif ($reaction === 'dislike') {
            $this->postRepository->setReaction($postId, $userId, false);
        } else {
            ValidationException::build()
                ->addError(ValidationErrorCode::INVALID_REACTION)
                ->throwIfAny();
        }
    }

    /**
     * Gets the current user's reaction for a post
     * @return string|null 'like', 'dislike' or null if no reaction
     */
    public function getUserReaction(int $postId, string $userId): ?string {
        return $this->postRepository->getUserReaction($postId, $userId);
    }

    /**
     * Toggle like on a post
     * If user already liked, removes it
     * If user disliked, changes to like
     * If user hasn't reacted, adds like
     * @return array with likes, dislikes, userReaction
     */
    public function toggleLike(int $postId, string $userId): array {
        $currentReaction = $this->getUserReaction($postId, $userId);
        if ($currentReaction === 'like') {
            // If already liked, remove it
            $this->postRepository->removeReaction($postId, $userId);
        } else {
            // If disliked or nothing, add like
            $this->postRepository->setReaction($postId, $userId, true);
        }
        return $this->getPostReactionStats($postId, $userId);
    }

    /**
     * Toggle dislike on a post
     * If user already disliked, removes it
     * If user liked, changes to dislike
     * If user hasn't reacted, adds dislike
     * @return array with likes, dislikes, userReaction
     */
    public function toggleDislike(int $postId, string $userId): array {
        $currentReaction = $this->getUserReaction($postId, $userId);
        if ($currentReaction === 'dislike') {
            // If already disliked, remove it
            $this->postRepository->removeReaction($postId, $userId);
        } else {
            // If liked or nothing, add dislike
            $this->postRepository->setReaction($postId, $userId, false);
        }
        return $this->getPostReactionStats($postId, $userId);
    }

    /**
     * Gets reaction statistics for a post
     * @return array with likes, dislikes, userReaction
     */
    private function getPostReactionStats(int $postId, string $userId): array {
        $likes = $this->postRepository->countLikes($postId);
        $dislikes = $this->postRepository->countDislikes($postId);
        $userReaction = $this->getUserReaction($postId, $userId);
        return [
            'likes' => $likes,
            'dislikes' => $dislikes,
            'userReaction' => $userReaction
        ];
    }

    /**
     * Deletes a post
     * 
     * @param int $postId Post ID to delete
     * @param string $userId User ID requesting deletion
     * @param bool $isAdmin If true, user is admin and can delete any post
     */
    public function deletePost(int $postId, string $userId, bool $isAdmin = false): void {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            ValidationException::build()
                ->addError(ValidationErrorCode::POST_NOT_FOUND)
                ->throwIfAny();
        }
        // Admin can delete any post, users can only delete their own
        if (!$isAdmin && $post->author->userId !== $userId) {
            throw new DomainException(DomainErrorCode::NOT_POST_OWNER);
        }
        $this->postRepository->delete($postId);
    }
}

