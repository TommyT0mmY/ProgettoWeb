<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\TagRepository;
use Unibostu\Model\DTO\TagDTO;
use Unibostu\Core\PostRepository;
use Unibostu\Model\Repository\PostTagRepository;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;

class TagService {
    private TagRepository $tagRepository;
    private PostTagRepository $postTagRepository;

    public function __construct() {
        $this->tagRepository = new TagRepository();
        $this->postTagRepository = new PostTagRepository();
    }

    /**
     * Recupera tutti i tag di un corso
     */
    public function getTagsByCourse(int $courseId): array {
        return $this->tagRepository->findByCourse($courseId);
    }

    /**
     * Searches tags by name within a specific course.
     *
     * @param string $searchTerm The search term
     * @param int $courseId The course ID to filter by
     * @return TagDTO[] Array of matching tags
     */
    public function searchTags(string $searchTerm, int $courseId): array {
        if (empty(trim($searchTerm))) {
            return $this->getTagsByCourse($courseId);
        }
        return $this->tagRepository->searchByName($searchTerm);
    }

    /**
     * Recupera i tag di un post
     * @return array Array di array con chiavi TagDTO
     */
    public function getTagsByPost(int $postId): array {
        return $this->postTagRepository->findTagsByPost($postId);
    }

    /**
     * Creates a new tag
     * @throws ValidationException if validation fails
     */
    public function createTag(string $tagName, int $courseId): void {
        $exceptionBuilder = ValidationException::build();
        if (empty($tagName)) {
            $exceptionBuilder->addError(ValidationErrorCode::TAG_REQUIRED);
        }

        if ($courseId <= 0) {
            $exceptionBuilder->addError(ValidationErrorCode::COURSE_REQUIRED);
        }

        $existing = $this->tagRepository->findByTypeAndCourse($tagName, $courseId);
        if ($existing) {
            $exceptionBuilder->addError(ValidationErrorCode::TAG_ALREADY_EXISTS);
        }
        $exceptionBuilder->throwIfAny();

        $this->tagRepository->save($tagName, $courseId);
    }

    /**
     * Updates a tag
     * @throws ValidationException if validation fails
     */
    public function updateTag(int $tagId, string $tagName, int $courseId): void {
        $exceptionBuilder = ValidationException::build();
        if (empty($tagName)) {
            $exceptionBuilder->addError(ValidationErrorCode::TAG_REQUIRED);
        }

        if ($courseId <= 0) {
            $exceptionBuilder->addError(ValidationErrorCode::COURSE_REQUIRED);
        }

        $tag = $this->tagRepository->findByIdAndCourse($tagId, $courseId);
        if (!$tag) {
            $exceptionBuilder->addError(ValidationErrorCode::TAG_REQUIRED);
        }
        $exceptionBuilder->throwIfAny();

        $this->tagRepository->update($tagId, $tagName);
    }

    /**
     * Deletes a tag
     * @throws ValidationException if validation fails
     */
    public function deleteTag(int $tagId, int $courseId): void {
        $exceptionBuilder = ValidationException::build();
        $tag = $this->tagRepository->findByIdAndCourse($tagId, $courseId);
        if (!$tag) {
            $exceptionBuilder->addError(ValidationErrorCode::TAG_REQUIRED);
        }
        $exceptionBuilder->throwIfAny();

        $this->tagRepository->delete($tagId);
    }
}
