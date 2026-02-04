<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Dashboard',
    'adminId' => $adminId
]);
?>

<header class="dashboard-header">
    <h2>Admin - Dashboard</h2>
    <button id="logout-button" class="btn btn-secondary">Logout</button>
</header>

<div class="post-container">
    <section class="post card" >
        <header>
            <h3>Faculties</h3>
        </header>
        <p>Number of Faculties: <?= h(count($faculties ?? [])) ?></p>
        <p>Manage Faculties, here you can add, edit, or delete faculties.
            For each faculty, you can view the associated courses.
            And for each course, you can view the associated tags.</p>
        <footer>
            <ul class="review">
                    <li>
                        <a class="btn btn-primary" href="/faculties">
                            View Faculties
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
    <section class="post card" >
        <header>
            <h3>Categories</h3>
        </header>
        <p>Number of Categories: <?= h(count($categories ?? [])) ?></p>
        <p>Manage Categories, here you can add, edit, or delete categories.</p>
        <footer>
            <ul class="review">
                    <li>
                        <a class="btn btn-primary" href="/categories">
                            View Categories
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
    <section class="post card" >
        <header>
            <h3>Users</h3>
        </header>
        <p>Number of Users: <?= h(count($users ?? [])) ?></p>
        <p>Manage Users, here you can add, ban, or delete users, that mainly are the students that use this platform.</p>
        <footer>
            <ul class="review">
                    <li>
                        <a class="btn btn-primary" href="/users">
                            View Users
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
</div>

