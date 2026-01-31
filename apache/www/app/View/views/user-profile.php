<?php
/**
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\PostDto[] $posts
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\UserDto $user
 * @var \Unibostu\Dto\UserDto $viewedUser
 * @var int|null $selectedCategoryId
 * @var string|null $selectedSortOrder
 */
$this->extend('main-layout', [
    'title' => 'Unibostu - User Profile',
    'userId' => $user->userId,
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
    ],
]);
$isOwnProfile = $user->userId === $viewedUser->userId;
?>

<article class="user-profile-details">
    <section>
    <h2>Personal details</h2>    
        <div class="profile"><?= htmlspecialchars(substr($viewedUser->firstName ?? '', 0, 1) . '.' . substr($viewedUser->lastName ?? '', 0, 1)); ?></div>
        <p>Username: <strong><?= htmlspecialchars($viewedUser->userId); ?></strong></p>
        <p>Name: <strong><?= htmlspecialchars($viewedUser->firstName ?? ''); ?></strong></p>
        <p>Last name: <strong><?= htmlspecialchars($viewedUser->lastName ?? ''); ?></strong></p>
        <?php if ($isOwnProfile): ?>
        <button type="button" onclick="window.location.href='/edit-profile'">Change info</button>   
        <?php endif; ?>
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
            <?php if ($isOwnProfile): ?>
            <button type="button" onclick="window.location.href='/select-courses'">Change chosen courses</button>
            <?php endif; ?>
    </section>
</article>
<article class="user-posts">
    <h2><?= $isOwnProfile ? "My Posts" : htmlspecialchars($viewedUser->firstName . "'s Posts") ?></h2>
    <?php if (empty($posts)): ?>
        <p>No posts to show.</p>
    <?php else: ?>
    <?= $this->component("posts-filter", [
        'action' => "/users/{$viewedUser->userId}",
        'categories' => $categories,
        'selectedCategoryId' => $selectedCategoryId,
        'selectedSortOrder' => $selectedSortOrder,
    ]) ?>
    <div class="post-container">
    <?php foreach ($posts ?? [] as $post): ?>
        <?= $this->component('post', ['post' => $post]) ?>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</article>
