<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\PostDto[] $posts
 * @var int $userId
 */

$this->extend('main-layout', [
    'title' => 'Unibostu - Homepage',
    'courses' => $courses,
    'userId' => $userId,
    'additionalHeadCode' => [
        '<script type="module" src="js/navbar-css.js"></script>',
        '<script type="module" src="/js/posts/main.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
        ],
    ]);
?>

    <section class="post-filters">
        <h3>Filters</h3>
        <form action="/homepage" method="get" id="filter-form">
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
    <hr/>
<div class="post_container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post]) ?>
<?php endforeach; ?>
</div>
