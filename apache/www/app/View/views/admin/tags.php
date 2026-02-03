<?php 
/**
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var \Unibostu\Dto\CourseDto $course
 * @var \Unibostu\Dto\TagDto[] $tags
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Tags',
    'adminId' => $adminId,
    'additionalHeadCode' => ['<script src="/js/admin/admin-actions.js" defer></script>']
]);
?>

<header>
    <h2>Admin - Tags Management</h2>
    <p>Faculty: <strong><?= h($faculty->facultyName) ?></strong> &bull; Course: <strong><?= h($course->courseName) ?></strong></p>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search tag" />
    <button type="submit">Search</button>
</form>
<button type="button" 
        data-action="add" 
        data-entity="tag" 
        data-url="/tags/add?courseId=<?= h($course->courseId) ?>">
    Add Tag
</button>

<div class="post-container cards">
<?php foreach ($tags ?? [] as $tag): ?>
    <section class="post card" >
        <header>
            <h3><?= h($tag->tagName ?? '') ?></h3>
        </header>
        <p>Tag ID: <?= h($tag->tagId ?? '') ?></p>
        <footer>
            <ul class="review">
                    <li>
                        <button type="button" 
                                data-action="edit" 
                                data-entity="tag" 
                                data-id="<?= h($tag->tagId) ?>"
                                data-url="/tags/<?= h($tag->tagId) ?>/edit">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                data-entity="tag" 
                                data-id="<?= h($tag->tagId) ?>"
                                data-url="/api/tags/<?= h($tag->tagId) ?>">
                            Delete
                        </button>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
