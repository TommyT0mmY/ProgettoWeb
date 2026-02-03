<?php 
/**
 * @var \Unibostu\Dto\FacultyDto[] $faculties
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Users',
    'adminId' => $adminId,
    'additionalHeadCode' => [
        '<script type="module" src="/js/ban-user.js"></script>',
        '<script src="/js/admin/admin-actions.js" defer></script>'
    ]
]);
?>

<header>
    <h2>Admin - Users Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search user" />
    <button type="submit">Search</button>
</form>

<div class="post-container">
<?php foreach ($users ?? [] as $user): ?>
    <section class="post card" >
        <header>
            <h3><?= h($user->userId) ?></h3>
        </header>
        <p>First Name: <?= h($user->firstName) ?></p>
        <p>Last Name: <?= h($user->lastName) ?></p>
        <?php if (isset($faculties[$user->userId])): ?>
        <p>Faculty: <?= h($faculties[$user->userId]->facultyName ) ?></p>
        <?php endif; ?>
        <p><?= h($user->suspended) ? 'Suspended' : 'Active' ?></p>
        <footer>
            <ul class="review">
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                data-entity="user" 
                                data-id="<?= h($user->userId) ?>"
                                data-url="/api/delete-user/<?= h($user->userId) ?>">
                            Delete
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="view" 
                                data-entity="user" 
                                data-id="<?= h($user->userId) ?>"
                                data-url="/users/<?= h($user->userId) ?>">
                            View Profile
                        </button>
                    </li>
                    <li>
                        <?php if ($user->suspended): ?>
                            <button type="button" 
                                    id="unban-user-btn-<?= h($user->userId) ?>" 
                                    class="unban-user-btn"
                                    data-user-id="<?= h($user->userId) ?>">
                                Unban
                            </button>
                        <?php else: ?>
                            <button type="button" 
                                    id="ban-user-btn-<?= h($user->userId) ?>" 
                                    class="ban-user-btn"
                                    data-user-id="<?= h($user->userId) ?>">
                                Ban
                            </button>
                        <?php endif; ?>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
