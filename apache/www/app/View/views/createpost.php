 <?php 
/**
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\TagDto[] $tags
 * @var \Unibostu\Dto\CourseDto $thisCourse
 * @var \Unibostu\Dto\CourseDto[] $courses
 */

$this->extend('main-layout', [
    'title' => 'Unibostu - Create post',
    'courses' => $courses,
    'userId' => $userId,
    'additionalHeadCode' => [
        '<link rel="stylesheet" href="/css/fsform.css" />',
        '<script type="module" src="/js/posts/create-post.js"></script>',
        '<link rel="stylesheet" href="/css/createpost.css" />',
        ] 
    ]); 
?>
<div class="create-post-container">
    <h1 class="createPost">Create post in <?= h($thisCourse->courseName) ?></h1>
    <form method="post" id="create-post-form" enctype="multipart/form-data" novalidate>
        <fieldset>
            <legend>Post information</legend>
            <output class="form-error-message" role="alert"></output>
            <div class="field-holder">
                <input type="text" name="title" id="title" aria-describedby="title-error" required />
                <label for="title">Post Title</label>
                <output class="field-error-message" id="title-error" for="title"></output>
            </div>
            <div class="field-holder">
                <select id="category" name="categoryId" aria-describedby="category-error">
                    <option value="">Select a category</option>
                    <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?=h($category->categoryId)?>"><?=h($category->categoryName)?></option>
                    <?php endforeach; ?>
                </select>
                <label for="category">Category</label>
                <output class="field-error-message" id="category-error" for="category"></output>
            </div>
        </fieldset>
        <fieldset>
            <legend>Topic tags <span class="field-optional">(optional)</span></legend>
            <?php foreach ($tags ?? [] as $tag): ?>
            <div class="checkbox-field">
                <input type="checkbox" name="tags[]" id="tag_<?= h($tag->tagId) ?>" value="<?= h($tag->tagId) ?>" />
                <label for="tag_<?= h($tag->tagId) ?>"><?= h($tag->tagName) ?></label>
            </div>
            <?php endforeach; ?>
        </fieldset>
        <fieldset>
            <legend>Post content</legend>
            <div>
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Write your post here..." required aria-describedby="description-error"></textarea>
                <output class="field-error-message" id="description-error" for="description"></output>
            </div>
            <div class="file-upload-section">
                <label for="attachments">Attachments <span class="field-optional">(optional)</span></label>
                <span id="attachments-hint">
                    Max 5 files, 10 MB each. Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP, RAR.
                </span>
                <input type="file" name="files[]" id="attachments" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar" aria-describedby="attachments-hint attachments-error"/>
                <output class="field-error-message" id="attachments-error" for="attachments"></output>
                <ul id="file-list" class="selected-files-list" aria-label="Selected files"></ul>
            </div>
        </fieldset>
        <input type="hidden" name="courseId" value="<?=h($thisCourse->courseId)?>"/>
        <output class="form-status-message" role="status"></output>
        <button type="submit" disabled>Create Post</button>
    </form>
</div>

<template id="file-item-template">
    <li>
        <span class="file-icon" aria-hidden="true">📎</span>
        <span class="file-name"></span>
        <span class="file-size"></span>
    </li>
</template>

