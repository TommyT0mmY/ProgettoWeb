<?php 
/**
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\TagDto[][] $tags
 * @var string $userId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Courses',
    'userId' => $userId
]);
?>

<header>
    <h2>Admin - Courses Management - Faculty: <?= h($faculty->facultyName) ?></h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search course" />
    <button type="submit">Search</button>
</form>
<button type="button">Add Course</button>

<div class="post-container cards">
<?php foreach ($courses ?? [] as $course): ?>
    <section class="Post card" >
        <header>
            <h3><?= h($course->courseName) ?></h3>
        </header>
            <p>Course ID: <?= h($course->courseId) ?></p>
            <p>Number of tags: <?= h(count($tags[$course->courseId] ?? [])) ?></p>
               
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
                    <li>
                        <a href="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/tags">
                            View Tags
                        </a>
                    </li>
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
<p><a href="/faculties/<?= h($faculty->facultyId) ?>">Go back to faculties</a></p>
