<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Faculties',
    'adminId' => $adminId
]);
?>

<header>
    <h2>Admin - Faculties Management</h2>
</header>
<form action="/faculties" method="GET">
    <input type="search" name="search" placeholder="Search faculty" />
    <button type="submit">Search</button>
</form>
<button type="button"><a href="/faculties/add">Add Faculty</a></button>

<div class="post-container cards">
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
                        <a href="/faculties/<?= h($faculty->facultyId) ?>/edit" >
                            Edit
                        </a>
                    </li>
                    <li>
                        <a href="#" >
                            Delete
                        </a>
                    </li>
                    <li>
                        <a href="/faculties/<?= h($faculty->facultyId) ?>/courses">
                            View Courses
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
