<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $userId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Dashboard',
    'userId' => $userId
]);
?>

<header>
    <h2>Admin - Dashboard</h2>
</header>

<div class="post-container cards">
    <section class="Post card" >
        <header>
            <h3>Faculties</h3>
        </header>
            <p>Manage Faculties, here you can add, edit, or delete faculties.</p>
            <p>For each faculty, you can view the associated courses.</p>
            <p>And for each course, you can view the associated tags.</p>
        <footer>
            <ul class="review">
                    <li>
                        <a href="/faculties">
                            View Faculties
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
    <section class="Post card" >
        <header>
            <h3>Categories</h3>
        </header>
            <p>Manage Categories, here you can add, edit, or delete categories.</p>
        <footer>
            <ul class="review">
                    <li>
                        <a href="/categories">
                            View Categories
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
    <section class="Post card" >
        <header>
            <h3>Users</h3>
        </header>
            <p>Manage Users, here you can add, edit, or delete users, that mainly are the students that use the platform.</p>
        <footer>
            <ul class="review">
                    <li>
                        <a href="/users">
                            View Users
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
</div>

