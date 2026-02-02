<?php 
/**
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\TagDto[][] $tags
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Courses',
    'adminId' => $adminId,
    'additionalHeadCode' => ['<script src="/js/admin/admin-actions.js" defer></script>']
]);
?>

<header>
    <h2>Admin - Courses Management</h2>
    <p>Faculty: <strong><?= h($faculty->facultyName) ?></strong></p>
</header>

<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search course" />
    <button type="submit">Search</button>
</form>
<p><a href="/faculties">Go back to faculties</a></p>
<button type="button" 
        data-action="add" 
        data-entity="course" 
        data-url="/faculties/<?= h($faculty->facultyId) ?>/courses/add">
    Add Course
</button>

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
                        <button type="button" 
                                data-action="edit" 
                                data-entity="course" 
                                data-id="<?= h($course->courseId) ?>"
                                data-url="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/edit">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                data-entity="course" 
                                data-id="<?= h($course->courseId) ?>"
                                data-url="/api/delete-course/<?= h($faculty->facultyId) ?>/<?= h($course->courseId) ?>">
                            Delete
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="view" 
                                data-entity="tag" 
                                data-id="<?= h($course->courseId) ?>"
                                data-url="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/tags">
                            View Tags
                        </button>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>

