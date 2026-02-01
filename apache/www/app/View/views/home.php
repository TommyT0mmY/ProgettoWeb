<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\PostDto[] $posts
 * @var int $userId
 * @var string|null $sortOrder
 * @var string|null $categoryId
 * @var string|null $selectedCategoryId
 * @var string $selectedSortOrder
 * @var bool $isAdmin
 */

// Use different layouts based on user role
$layout = $isAdmin ? 'admin-layout' : 'main-layout';
$layoutParams = [
    'title' => 'Unibostu - Homepage',
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
    'userId' => $userId,
];

// Add courses only for non-admin users (main-layout requires it)
if (!$isAdmin) {
    $layoutParams['courses'] = $courses;
}

$this->extend($layout, $layoutParams);
?>

<?= $this->component("posts-filter", [
    'action' => "/",
    'categories' => $categories,
    'selectedCategoryId' => $selectedCategoryId,
    'selectedSortOrder' => $selectedSortOrder,
]) ?>
<div class="post-container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post, 'forAdmin' => $isAdmin, 'currentPageUrl' => '/']) ?>
<?php endforeach; ?>
</div>
