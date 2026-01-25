<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\PostDto[] $posts
 */

$this->extend('main-layout', [
    'title' => 'Unibostu - Community',
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
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
                    <option value="<?= htmlspecialchars($category->categoryId) ?>"><?= htmlspecialchars($category->categoryName) ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="ordering">Order by date:</label>
            <select id="ordering" name="sortOrder">
                <option value="desc">Newest post first</option>
                <option value="asc">Oldest post first</option>
            </select>
        </p>

        <p><input type="submit" value="Filter" /></p>
    </form>
</section>

<div class="post_container">
    <?php foreach ($posts ?? [] as $post): ?>
        <?= $this->component('post', ['post' => $post]) ?>
    <?php endforeach; ?>
</div>
