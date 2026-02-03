<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostDTO {
    public UserDTO $author;
    public int $postId;
    public string $title;
    public string $description;
    /** @var AttachmentDTO[] Array of attachment DTOs */
    public array $attachments;
    public string $createdAt;
    public string $userId;
    public CourseDTO $course;
    /** @var array Array of tag arrays with TagDTO keys */
    public array $tags;
    /** @var ?CategoryDTO  category */
    public ?CategoryDTO $category;
    public int $likes;
    public int $dislikes;
    /** @var bool value is 0 if disliked, 1 if liked, null if no action taken */ 
    public ?bool $likedByUser;

    public function __construct(
        int $postId,
        UserDTO $author,
        string $title,
        string $description,
        string $createdAt,
        CourseDTO $course,
        ?CategoryDTO $category,
        array $tags = [],
        int $likes = 0,
        int $dislikes = 0,
        ?bool $likedByUser = null,
        array $attachments = []
    ) {
        $this->postId = $postId;
        $this->author = $author;
        $this->title = $title;
        $this->description = $description;
        $this->attachments = $attachments;
        $this->createdAt = $createdAt;
        $this->course = $course;
        $this->tags = $tags;
        $this->category = $category;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->likedByUser = $likedByUser;
    }
}
