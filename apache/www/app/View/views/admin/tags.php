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
    <h2>Admin - Tags Management - Faculty: <?= h($faculty->facultyName) ?>, Course: <?= h($course->courseName) ?></h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search tag" />
    <button type="submit">Search</button>
</form>
<button type="button" 
        data-action="add" 
        data-entity="tag" 
        data-url="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/tags/add">
    Add Tag
</button>

<div class="post-container cards">
<?php foreach ($tags ?? [] as $tag): ?>
    <section class="post card" >
        <header>
            <h3><?= h($tag->tag_name ?? '') ?></h3>
        </header>
        <p>Tag ID: <?= h($tag->tagId ?? '') ?></p>
        <div class="post-metadata">
            <div class="metadata-section" data-section="course">
                <span class="metadata-label">Course:</span>
                <ul class="metadata-list course-list">
                    <li class="tag subject"><a href="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/tags"><?= h($course->courseName) ?></a></li>
                </ul>
            </div>
            <div class="metadata-section" data-section="course">
                <span class="metadata-label">Faculty:</span>
                <ul class="metadata-list course-list">
                    <li class="tag subject"><a href="/faculties/<?= h($faculty->facultyId) ?>/courses"><?= h($faculty->facultyName) ?></a></li>
                </ul>
            </div>button type="button" 
                                data-action="edit" 
                                data-entity="tag" 
                                data-id="<?= h($tag->tagId) ?>"
                                data-url="/faculties/<?= h($faculty->facultyId) ?>/courses/<?= h($course->courseId) ?>/tags/<?= h($tag->tagId) ?>/edit">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                data-entity="tag" 
                                data-id="<?= h($tag->tagId) ?>"
                                data-url="/api/delete-tag/<?= h($faculty->facultyId) ?>/<?= h($course->courseId) ?>/<?= h($tag->tagId) ?>">
                            Delete
                        </button>
                    </li><li>
                        <a href="#" >
                            Delete
                        </a>
                    </li>
                    
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
