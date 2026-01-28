<?php
/**
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\PostDto[] $posts
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\UserDto $user
 */
$this->extend('main-layout', [
    'title' => 'Unibostu - User Profile',
    'userId' => $user->userId,
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
]);
?>

<div class="container">  

    <section>
    <h2>Personal details</h2>    
        <div class="profile"><?= htmlspecialchars(substr($user->firstName ?? '', 0, 1) . '.' . substr($user->lastName ?? '', 0, 1)); ?></div>
        <p>Username: <strong><?= htmlspecialchars($user->userId); ?></strong></p>
        <p>Name: <strong><?= htmlspecialchars($user->firstName ?? ''); ?></strong></p>
        <p>Last name: <strong><?= htmlspecialchars($user->lastName ?? ''); ?></strong></p>
        <button type="button">Change info</button>   
    </section>

    <section>
    <h2>University details</h2>         
        <p>Faculty: <strong><?= htmlspecialchars($faculty->facultyName ?? ''); ?></strong></p> 
        <p>Chosen courses:</p>
            <?php if (!empty($courses)): ?>
                <ul class="tags">
                    <?php foreach ($courses as $course): ?>
                    <li class="tag subject"><a href="/courses/<?= htmlspecialchars($course->courseId) ?>"><?= htmlspecialchars($course->courseName) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <p><a href="/studentpreferences">Change chosen courses</a></p>         
    </section>

</div>

<section>
    <h2>My posts</h2>

    <div class="post-filters">
        <h3>Filters</h3>
        <form action="/users/<?= htmlspecialchars($user->userId) ?>" method="get" id="filter-form">
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
    </div>

    <div class="post_container">
    <?php foreach ($posts ?? [] as $post): ?>
        <?= $this->component('post', ['post' => $post]) ?>
    <?php endforeach; ?>
    </div>
</section>
