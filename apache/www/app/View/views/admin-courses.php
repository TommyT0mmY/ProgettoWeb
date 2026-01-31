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
    <h2>Admin - Courses Management - Faculty: <?= htmlspecialchars($faculty->facultyName) ?></h2>
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
            <h3><?= htmlspecialchars($course->courseName) ?></h3>
        </header>
            <p>Course ID: <?= htmlspecialchars($course->courseId) ?></p>
            <p>Number of tags: <?= htmlspecialchars(count($tags[$course->courseId] ?? [])) ?></p>
               
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
                        <a href="/faculties/<?= htmlspecialchars($faculty->facultyId) ?>/courses/<?= htmlspecialchars($course->courseId) ?>/tags">
                            View Tags
                        </a>
                    </li>
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
<p><a href="/faculties/<?= htmlspecialchars($faculty->facultyId) ?>">Go back to faculties</a></p>
