<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\TagDto[] $tags
 * @var \Unibostu\Dto\PostDto[] $posts
 * @var \Unibostu\Dto\CourseDto $thisCourse
 * @var string $userId
 * @var string|null $selectedCategoryId
 * @var string $selectedSortOrder
 * @var array $selectedTags
 */
$this->extend('main-layout', [
    'title' => 'Unibostu - Community',
    'userId' => $userId,
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
]);
?>

<h2><?= h($thisCourse->courseName) ?></h2>
<p><a href="/courses/<?= h($thisCourse->courseId) ?>/createpost">Create new post</a></p>
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
    <?= $this->component('post', ['post' => $post]) ?>
<?php endforeach; ?>
</div>
