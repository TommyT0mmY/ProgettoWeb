<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\PostDto[] $posts
 * @var int $userId
 * @var string|null $sortOrder
 * @var string|null $categoryId
 */

$this->extend('admin-layout', [
    'title' => 'Unibostu - Admin Homepage',
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
    'userId' => $userId
]);
?>

    <section class="post-filters">
        <h3>Filters</h3>
        <form action="/" method="get" id="filter-form">
            <p>
                <label for="filter-type">Category:</label>
                <select id="filter-type" name="categoryId">
                    <option value="">All categories</option>
                    <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= htmlspecialchars($category->categoryId) ?>" 
                            <?= (isset($categoryId) && $categoryId == $category->categoryId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->categoryName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="ordering">Order by date:</label>
                <select id="ordering" name="sortOrder">
                    <option value="desc" <?= (!isset($sortOrder) || strtolower($sortOrder) === 'desc') ? 'selected' : '' ?>>Newest post first</option>
                    <option value="asc" <?= (isset($sortOrder) && strtolower($sortOrder) === 'asc') ? 'selected' : '' ?>>Oldest post first</option>
                </select>
            </p>

            <p><input type="submit" value="Filter" /></p>
        </form>
    </section>
    <hr/>
<div class="post-container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post]) ?>
<?php endforeach; ?>
</div>
