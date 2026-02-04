<?php
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\CourseDTO[] $courses User's subscribed courses (only for non-admin users)
 * @var \Unibostu\Model\DTO\FacultyDTO $faculty Faculty details of the viewed user
 * @var \Unibostu\Model\DTO\PostDTO[] $posts Posts by the viewed user
 * @var \Unibostu\Model\DTO\CategoryDTO[] $categories All available categories
 * @var \Unibostu\Model\DTO\UserDTO $user Current logged-in user
 * @var \Unibostu\Model\DTO\UserDTO $viewedUser The user whose profile is being viewed
 * @var int|null $selectedCategoryId Selected category ID from filters
 * @var string|null $selectedSortOrder Selected sort order (asc/desc)
 * @var bool $isAdmin Whether the current user is an admin
 */

// Use different layouts based on user role
$layout = $isAdmin ? 'admin-layout' : 'main-layout';
$layoutParams = [
    'title' => 'Unibostu - User Profile',
    'userId' => $user->userId,
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/multi-post.js"></script>',
        '<script type="module" src="/js/ban-user.js"></script>',
        '<link rel="stylesheet" href="/css/profile.css" />',
    ],
];

// Add courses only for non-admin users (main-layout requires it)
if (!$isAdmin) {
    $layoutParams['courses'] = $courses;
}

$this->extend($layout, $layoutParams);
$isOwnProfile = !$isAdmin && $user->userId === $viewedUser->userId;
?>

<article class="user-profile-details">
    <section>
    <h2>Personal details</h2>
        <div class="container">    
        <div class="profile"><?= h(substr($viewedUser->firstName ?? '', 0, 1) . '.' . substr($viewedUser->lastName ?? '', 0, 1)); ?></div>
        <section>
        <p>Username: <strong><?= h($viewedUser->userId); ?></strong></p>
        <p>Name: <strong><?= h($viewedUser->firstName ?? ''); ?></strong></p>
        <p>Last name: <strong><?= h($viewedUser->lastName ?? ''); ?></strong></p>
        </section>
        </div>
        <?php if ($viewedUser->suspended): ?>
        <p class="user-banned">User banned</p>
        <?php endif; ?>
        <?php if ($isOwnProfile): ?>
        <button class="btn btn-primary" type="button" onclick="window.location.href='/edit-profile'">Change info</button>   
        <?php endif; ?>
        <?php if ($isAdmin && !$viewedUser->suspended): ?>
        <button type="button" id="ban-user-btn" class="btn btn-danger" data-user-id="<?= h($viewedUser->userId) ?>">Ban User</button>
        <?php endif; ?>
        <?php if ($isAdmin && $viewedUser->suspended): ?>
        <button type="button" id="unban-user-btn" class="btn btn-danger" data-user-id="<?= h($viewedUser->userId) ?>">Unban User</button>
        <?php endif; ?>
    </section>
    <section>
    <h2>University details</h2>         
        <p>Faculty: <strong><?= h($faculty->facultyName ?? ''); ?></strong></p> 
        <?php if (!$isAdmin): ?>
        <p>Chosen courses:</p>
            <?php if (!empty($courses)): ?>
                <ul class="metadata-list">
                    <?php foreach ($courses as $course): ?>
                    <li class="tag subject"><a href="/courses/<?= h($course->courseId) ?>"><?= h($course->courseName) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($isOwnProfile): ?>
            <button type="button" class="btn btn-primary" onclick="window.location.href='/select-courses'">Change chosen courses</button>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</article>
<hr class="divider"/>
<article class="user-posts">
    <h2><?= $isOwnProfile ? "My Posts" : h($viewedUser->firstName . "'s Posts") ?></h2>
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
        <?= $this->component('post', ['post' => $post, 'forAdmin' => $isAdmin, 'currentPageUrl' => "/users/{$viewedUser->userId}"]) ?>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</article>
