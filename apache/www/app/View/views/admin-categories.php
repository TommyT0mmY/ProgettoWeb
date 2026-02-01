<?php 
/**
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var string $userId
*/

$this->extend('admin-layout', [
    'title' => 'Unibostu - Categories',
    'userId' => $userId
]);
?>

<header>
    <h2>Admin - Categories Management</h2>
</header>
<form action="#" method="GET">
    <input type="search" name="search" placeholder="Search category" />
    <button type="submit">Search</button>
</form>
<button type="button">Add Category</button>

<div class="post-container cards">
<?php foreach ($categories ?? [] as $category): ?>
    <section class="Post card" >
        <header>
            <h3><?= h($category->categoryName) ?></h3>
        </header>
            <p>Category ID: <?= h($category->categoryId) ?></p>   
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
            </ul>            
    </footer>
    </section>
<?php endforeach; ?>
</div>
