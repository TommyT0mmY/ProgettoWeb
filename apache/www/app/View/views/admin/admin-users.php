<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $userId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Users',
    'userId' => $userId
]);
?>

<header>
    <h2>Admin - Users Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search user" />
    <button type="submit">Search</button>
</form>
<button type="button">Add User</button>

<div class="post-container cards">
<?php foreach ($users ?? [] as $user): ?>
    <section class="Post card" >
        <header>
            <h3><?= htmlspecialchars($user->userId) ?></h3>
        </header>
            <p>First Name: <?= htmlspecialchars($user->firstName) ?></p>
            <p>Last Name: <?= htmlspecialchars($user->lastName) ?></p>
            <p>Faculty: <?= htmlspecialchars($faculties[$user->userId]->facultyName ) ?></p>

        <footer>
            <ul class="review">
                    <li>
                        <a href="#" >
                            Delete
                        </a>
                    </li>
                    <li>
                        <a href="/users/<?= htmlspecialchars($user->userId) ?>">
                            View Profile
                        </a>
                    </li>
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
<p><a href="/">Go back to homepage</a></p>
