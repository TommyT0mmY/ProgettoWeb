<?php 
/** 
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\CourseDTO[] $courses User's subscribed courses (only for non-admin users)
 * @var \Unibostu\Model\DTO\CategoryDTO[] $categories All available categories
 * @var \Unibostu\Model\DTO\TagDTO[] $tags Tags available for this course
 * @var \Unibostu\Model\DTO\PostDTO[] $posts Posts in this course
 * @var \Unibostu\Model\DTO\CourseDTO $thisCourse The current course details
 * @var string $userId Current user ID
 * @var string|null $selectedCategoryId Selected category ID from filters
 * @var string $selectedSortOrder Selected sort order (asc/desc)
 * @var array $selectedTags Selected tag IDs
 * @var bool $isAdmin Whether the current user is an admin
 */

// Use different layouts based on user role
$layout = $isAdmin ? 'admin-layout' : 'main-layout';
$layoutParams = [
    'title' => 'Unibostu - Course',
    'userId' => $userId,
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
];

// Add courses only for non-admin users (main-layout requires it)
if (!$isAdmin) {
    $layoutParams['courses'] = $courses;
}

$this->extend($layout, $layoutParams);
?>

<div class="course-header">
    <h2><?= h($thisCourse->courseName) ?></h2>
    <?php if (!$isAdmin): ?>
    <a href="/courses/<?= h($thisCourse->courseId) ?>/createpost" class="btn-create-post">+ Create new post</a>
    <?php endif; ?>
</div>
<?= $this->component("posts-filter", [
    'action' => "/courses/{$thisCourse->courseId}",
    'categories' => $categories,
    'tags' => $tags,
    'selectedCategoryId' => $selectedCategoryId,
    'selectedSortOrder' => $selectedSortOrder,
    'selectedTags' => $selectedTags,
]) ?>
<div class="post-container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post, 'forAdmin' => $isAdmin, 'currentPageUrl' => "/courses/{$thisCourse->courseId}"]) ?>
<?php endforeach; ?>
</div>
