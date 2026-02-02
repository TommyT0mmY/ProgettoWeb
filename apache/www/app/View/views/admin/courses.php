<?php 
/**
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\TagDto[][] $tags
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Courses',
    'adminId' => $adminId
]);
?>

<header>
    <h2>Admin - Courses Management - Faculty: <?= h($faculty->facultyName) ?></h2>
</header>

<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search course" />
    <button type="submit">Search</button>
</form>
<p><a href="/faculties">Go back to faculties</a></p>
<button type="button">Add Course</button>

<div class="post-container cards">
<?php foreach ($courses ?? [] as $course): ?>
    <section class="post card" >
        <header>
            <h3><?= h($course->courseName) ?></h3>
        </header>
        <p>Course ID: <?= h($course->courseId) ?></p>
        <p>Number of tags: <?= h(count($tags[$course->courseId] ?? [])) ?></p>               
        <footer>
            <ul class="review">
                    <li>
                        <a href="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/edit" >
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

