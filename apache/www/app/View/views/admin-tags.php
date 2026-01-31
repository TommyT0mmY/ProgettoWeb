<?php 
/**
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\TagDto[] $tags
 * @var string $userId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Tags',
    'userId' => $userId
]);
?>

<header>
    <h2>Admin - Tags Management - Faculty: <?= htmlspecialchars($faculty->facultyName) ?>, Course: <?= htmlspecialchars($course->courseName) ?></h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search tag" />
    <button type="submit">Search</button>
</form>
<button type="button">Add Tag</button>

<div class="post-container cards">
<?php foreach ($tags ?? [] as $tag): ?>
    <section class="Post card" >
        <header>
            <h3><?= htmlspecialchars($tag->tag_name ?? '') ?></h3>
        </header>
            <p>Tag ID: <?= htmlspecialchars($tag->tagId ?? '') ?></p>
            <a href="/faculties/<?= htmlspecialchars($faculty->facultyId) ?>/courses/<?= htmlspecialchars($course->courseId) ?>/tags">Course: <?= htmlspecialchars($course->courseName) ?></a>
            <div class="post-metadata">
                <div class="metadata-section" data-section="community">
                    <span class="metadata-label">Faculty:</span>
                    <ul class="metadata-list community-list">
                        <li class="tag subject"><a href="/faculties/<?= htmlspecialchars($faculty->facultyId) ?>/courses"><?= htmlspecialchars($faculty->facultyName) ?></a></li>
                    </ul>
                </div>
            </div>
             <footer>
            <ul class="review">
                    <li>
                        <a href="#" >
                            Edit
                        </a>
                    </li>
                    <li>
                        <a href="#" >
                            Delete
                        </a>
                    </li>
                    
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
<p><a href="/faculties/<?= htmlspecialchars($faculty->facultyId) ?>/courses">Go back to courses</a></p>
