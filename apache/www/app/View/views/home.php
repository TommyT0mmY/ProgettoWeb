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
 * @var bool isAdmin
 */

$this->extend('main-layout', [
    'title' => 'Unibostu - Homepage',
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
    'userId' => $userId,
    'courses' => $courses,
]);
?>

<?= $this->component("posts-filter", [
    'action' => "/",
    'categories' => $categories,
    'selectedCategoryId' => $selectedCategoryId,
    'selectedSortOrder' => $selectedSortOrder,
]) ?>
<div class="post-container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post]) ?>
<?php endforeach; ?>
</div>
