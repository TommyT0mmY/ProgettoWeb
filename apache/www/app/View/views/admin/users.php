<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Users',
    'adminId' => $adminId
]);
?>

<header>
    <h2>Admin - Users Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search user" />
    <button type="submit">Search</button>
</form>

<div class="post-container cards">
<?php foreach ($users ?? [] as $user): ?>
    <section class="post card" >
        <header>
            <h3><?= h($user->userId) ?></h3>
        </header>
        <p>First Name: <?= h($user->firstName) ?></p>
        <p>Last Name: <?= h($user->lastName) ?></p>
        <p>Faculty: <?= h($faculties[$user->userId]->facultyName ) ?></p>
        <p><?= h($user->suspended) ? 'Suspended' : 'Active' ?></p>
        <footer>
            <ul class="review">
                    <li>
                        <a href="#" >
                            Delete
                        </a>
                    </li>
                    <li>
                        <a href="/users/<?= h($user->userId) ?>">
                            View Profile
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Suspend 
                        </a>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
