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

<h2><?= htmlspecialchars($thisCourse->courseName) ?></h2>

<p><a href="/courses/<?= htmlspecialchars($thisCourse->courseId) ?>/createpost">Create new post</a></p>

<section class="post-filters">
    <h3>Filters</h3>
    <form action="/courses/<?= htmlspecialchars($thisCourse->courseId) ?>" method="get" id="filter-form">
        <p>
            <label for="filter-type">Category:</label>
            <select id="filter-type" name="categoryId">
                <option value="">All categories</option>
                <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?= htmlspecialchars($category->categoryId) ?>" <?= $selectedCategoryId == $category->categoryId ? 'selected' : '' ?>><?= htmlspecialchars($category->categoryName) ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="ordering">Order by date:</label>
            <select id="ordering" name="sortOrder">
                <option value="desc" <?= $selectedSortOrder === 'desc' ? 'selected' : '' ?>>Newest post first</option>
                <option value="asc" <?= $selectedSortOrder === 'asc' ? 'selected' : '' ?>>Oldest post first</option>
            </select>
        </p>

        <fieldset>
            <legend>Filter by tags:</legend>
            <?php foreach ($tags ?? [] as $tag): ?>
                <p>
                    <input type="checkbox" name="tags[]" id="tag_<?= htmlspecialchars($tag->tagId) ?>" value="<?= htmlspecialchars($tag->tagId) ?>" <?= in_array($tag->tagId, $selectedTags) ? 'checked' : '' ?> />
                    <label for="tag_<?= htmlspecialchars($tag->tagId) ?>"><?= htmlspecialchars($tag->tag_name) ?></label>
                </p>
            <?php endforeach; ?>
        </fieldset>

        <p><input type="submit" value="Filter" /></p>
    </form>
</section>

<div class="post_container">
    <?php foreach ($posts ?? [] as $post): ?>
        <?= $this->component('post', ['post' => $post]) ?>
    <?php endforeach; ?>
</div>
