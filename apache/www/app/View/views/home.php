<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\PostDto[] $posts
 */

$this->extend('main-layout', [
    'title' => 'Unibostu - Homepage',
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
        ],
    ]);
?>

    <section class="post-filters">
        <header>
            <h3>Filters</h3>
        </header>
        <form action="/homepage" method="GET" id="filter-form">
            <label for="filter-type">Category</label>
            <select id="filter-type" name="categoryId">
            <option  value="">All categories</option>
            <?php foreach ($categories ?? [] as $category): ?>
                <option value="<?= htmlspecialchars($category->categoryId) ?>"><?= htmlspecialchars($category->categoryName) ?></option>
            <?php endforeach; ?>
            </select>

            <label for="ordering">Order by date</label>
            <select id="ordering" name="sortOrder">
                <option id="tag_sorting" value="desc">Newest post first</option>
                <option id="tag_sorting" value="asc">Oldest post first</option>
            </select>

            <input type="submit" value="Filter"/>
        </form>
    </section>
    <hr/>
<div class="post_container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post]) ?>
<?php endforeach; ?>
</div>
