<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Faculties',
    'adminId' => $adminId,
    'additionalHeadCode' => ['<script src="/js/admin/admin-actions.js" defer></script>']
]);
?>

<header>
    <h2>Admin - Faculties Management</h2>
</header>
<form action="/faculties" method="GET">
    <input type="search" name="search" placeholder="Search faculty" />
    <button type="submit" class="btn btn-secondary">Search</button>
</form>
<button type="button" class="btn btn-primary" data-action="add" data-entity="faculty" data-url="/faculties/add">Add Faculty</button>

<div class="post-container">
<?php foreach ($faculties ?? [] as $faculty): ?>
    <section class="post card" >
        <header>
            <h3><?= h($faculty->facultyName) ?></h3>
        </header>
        <p>Faculty ID: <?= h($faculty->facultyId) ?></p>
        <p>Number of Courses: <?= h(count($courses[$faculty->facultyId] ?? [])) ?></p>   
        <footer>
            <ul class="review">
                    <li>
                        <button type="button" 
                                data-action="edit" 
                                data-entity="faculty" 
                                class="btn btn-primary"
                                data-id="<?= h($faculty->facultyId) ?>"
                                data-url="/faculties/<?= h($faculty->facultyId) ?>/edit">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                class="btn btn-danger"
                                data-entity="faculty" 
                                data-id="<?= h($faculty->facultyId) ?>"
                                data-url="/api/faculties/<?= h($faculty->facultyId) ?>">
                            Delete
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="view" 
                                class="btn btn-secondary"
                                data-entity="course" 
                                data-id="<?= h($faculty->facultyId) ?>"
                                data-url="/courses?facultyId=<?= h($faculty->facultyId) ?>">
                            View Courses
                        </button>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
