<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\CategoryDTO $category
 * @var string $adminId
 */

$this->extend('loggedout-layout', [
    'title' => 'Edit Category - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/edit-category.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form class="fullscreen-form" id="edit-category-form" method="post" novalidate>
    <fieldset>
        <legend>Edit Category</legend>
        <output class="form-error-message" for="categoryname" role="alert"></output>
        
        <div class="field-holder">
            <input type="text" name="categoryid" id="categoryid" value="<?= h($category->categoryId); ?>" disabled />
            <label for="categoryid">Category ID</label>
        </div>
        
        <div class="field-holder">
            <input type="text" name="categoryname" id="categoryname" aria-describedby="categoryname-error" value="<?= h($category->categoryName ?? ''); ?>" required />
            <label for="categoryname">Category Name</label>
            <output class="field-error-message" id="categoryname-error" for="categoryname"></output>
        </div>
        
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>" />
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>" />
        <input type="hidden" name="categoryid" value="<?= h($category->categoryId); ?>" />

        <output class="form-status-message" role="status"></output>
        
        <div class="controls-container">            
            <button type="button" onclick="window.location.href='/categories'">Back</button>
            <button type="submit" id="edit-categories-submit" disabled>Save Changes</button>
        </div>
        
    </fieldset>
</form>
