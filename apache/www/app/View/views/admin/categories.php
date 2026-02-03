<?php 
/**
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var string $adminId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Categories',
    'adminId' => $adminId,
    'additionalHeadCode' => ['<script src="/js/admin/admin-actions.js" defer></script>']
]);
?>

<header>
    <h2>Admin - Categories Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search category" />
    <button type="submit">Search</button>
</form>
<button type="button" data-action="add" data-entity="category" data-url="/categories/add">Add Category</button>

<div class="post-container">
<?php foreach ($categories ?? [] as $category): ?>
    <section class="post card" >
        <header>
            <h3><?= h($category->categoryName) ?></h3>
        </header>
        <p>Category ID: <?= h($category->categoryId) ?></p>   
        <footer>
            <ul class="review">
                    <li>
                        <button type="button" 
                                data-action="edit" 
                                data-entity="category" 
                                data-id="<?= h($category->categoryId) ?>"
                                data-url="/categories/<?= h($category->categoryId) ?>/edit">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" 
                                data-action="delete" 
                                data-entity="category" 
                                data-id="<?= h($category->categoryId) ?>"
                                data-url="/api/categories/<?= h($category->categoryId) ?>">
                            Delete
                        </button>
                    </li>
            </ul>            
        </footer>
    </section>
<?php endforeach; ?>
</div>
