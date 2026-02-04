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
    <button type="submit" class="btn btn-secondary">Search</button>
</form>
<button type="button" 
        data-action="add" 
        data-entity="course" 
        class="btn btn-primary"
        data-url="/courses/add?facultyId=<?= h($faculty->facultyId) ?>">
    Add Course
</button>

<div class="post-container">
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
                                class="btn btn-primary"
                                data-entity="course" 
                                data-id="<?= h($course->courseId) ?>"
                                data-url="/courses/<?= h($course->courseId) ?>/edit">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                class="btn btn-danger"
                                data-entity="course" 
                                data-id="<?= h($course->courseId) ?>"
                                data-url="/api/courses/<?= h($course->courseId) ?>">
                            Delete
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="view" 
                                class="btn btn-secondary"
                                data-entity="tag" 
                                data-id="<?= h($course->courseId) ?>"
                                data-url="/tags?courseId=<?= h($course->courseId) ?>">
                            View Tags
                        </button>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>

