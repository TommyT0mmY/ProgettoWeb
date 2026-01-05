<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostDTO {
    public UserDTO $author;
    public int $postId;
    public string $title;
    public string $description;
    public ?string $attachmentPath;
    public string $createdAt;
    public string $userId;
    public int $courseId;
    /** @var array Array of tag arrays with 'type' keys */
    public array $tags;
    /** @var ?int  category id */
    public ?int $category;
    public int $likes;
    public int $dislikes;
    /** @var bool value is 0 if disliked, 1 if liked, null if no action taken */ 
    public ?bool $likedByUser;

    public function __construct(
        int $postId,
        string $title,
        string $description,
        string $createdAt,
        string $userId,
        int $courseId,
        ?int $category,
        array $tags = [],
        int $likes = 0,
        int $dislikes = 0,
        ?bool $likedByUser = null,
        ?string $attachmentPath = null
    ) {
        $this->postId = $postId;
        $this->title = $title;
        $this->description = $description;
        $this->attachmentPath = $attachmentPath;
        $this->createdAt = $createdAt;
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->tags = $tags;
        $this->category = $category;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->likedByUser = $likedByUser;
    }
}
