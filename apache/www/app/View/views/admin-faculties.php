<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $userId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Student Preferences',
    'userId' => $userId
]);
?>

<header>
    <h2>Admin - Faculties Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search faculty" />
    <button type="submit">Search</button>
</form>
<button type="button">Add Faculty</button>

<div class="post_container">
<?php foreach ($faculties ?? [] as $faculty): ?>
    <section class="Post card" >
        <header>
            <h3><?= htmlspecialchars($faculty->facultyName) ?></h3>
        </header>
            <p>Faculty ID: <?= htmlspecialchars($faculty->facultyId) ?></p>
            <p>Number of Courses: <?= htmlspecialchars(count($courses ?? [])) ?></p>   
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
                        <a href="#">
                            View Courses
                        </a>
                    </li>
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
<p><a href="/">Go back to homepage</a></p>
