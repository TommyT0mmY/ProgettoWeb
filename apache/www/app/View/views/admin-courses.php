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
    <h2>Admin - Courses Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search course" />
    <button type="submit">Search</button>
</form>
<button type="button">Add Course</button>

<div class="post_container cards">
<?php foreach ($courses ?? [] as $course): ?>
    <section class="Post card" >
        <header>
            <h3><?= htmlspecialchars($course->courseName) ?></h3>
        </header>
            <p>Course ID: <?= htmlspecialchars($course->courseId) ?></p>
            <p>Faculty ID: <?= htmlspecialchars($course->facultyId) ?></p>
               
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
                            View Tags
                        </a>
                    </li>
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
<p><a href="/">Go back to homepage</a></p>
