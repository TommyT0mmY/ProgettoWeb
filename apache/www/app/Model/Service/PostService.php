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
     * Gets posts matching filters.
     *
     * @param PostQuery $postQuery Query filters.
     * @return PostDTO[] Matching posts.
     */
    public function getPosts(PostQuery $postQuery): array {
        return $this->postRepository->findWithFilters($postQuery);
    }

    /**
     * Gets a single post with details.
     *
     * @param int $postId Post ID.
     * @param string|null $userId User ID to populate likedByUser.
     * @return PostDTO|null Post or null if not found.
     */
    public function getPostDetails(int $postId, ?string $userId = null): ?PostDTO {
        return $this->postRepository->findById($postId, $userId);
    }

    /**
     * Creates a new post.
     * 
     * Posts are always posted inside a course, and the user must be enrolled in that course.
     * Tags must belong to the selected course.
     * Categories are optional.
     *
     * @param CreatePostDTO $dto Post data.
     * @return int Created post ID.
     * @throws ValidationException When user does not exist.
     * @throws ValidationException When user is not enrolled in the course.
     * @throws ValidationException When tags do not belong to the course.
     * @throws ValidationException When title is empty.
     * @throws ValidationException When description is empty.
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
     * Gets user's reaction on a post.
     *
     * @param int $postId Post ID.
     * @param string $userId User ID.
     * @return string|null "like", "dislike", or null.
     */
    public function getUserReaction(int $postId, string $userId): ?string {
        return $this->postRepository->getUserReaction($postId, $userId);
    }

    /**
     * Toggles like on a post.
     *
     * Removes like if already liked, adds like otherwise.
     *
     * @param int $postId Post ID.
     * @param string $userId User ID.
     * @return array Stats with likes, dislikes, userReaction.
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
     * Deletes a post.
     *
     * Admins can delete any post, users only their own.
     *
     * @param int $postId Post ID to delete.
     * @param string $userId User ID requesting deletion.
     * @param bool $isAdmin Whether user is admin.
     * @throws ValidationException When post does not exist.
     * @throws DomainException When user is not the post owner and not admin.
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

