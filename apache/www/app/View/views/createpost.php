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
        ] 
    ]); 
?>

<div class="create-post-container">
    <a href="/courses/<?= h($thisCourse->courseId) ?>" class="back-link">← Go back</a>
    
    <h2 class="createPost">Create post for: <?= h($thisCourse->courseName) ?></h2>

<form method="post" id="create-post-form" class="create-post-form" novalidate>
    <input type="hidden" name="courseId" value="<?= h($thisCourse->courseId) ?>" />
    <fieldset>
        <legend>Post information</legend>
        <output class="form-error-message" role="alert"></output>
        <p>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" placeholder="Insert post title" required />
            <output id="title-error" class="field-error-message"></output>
        </p>
        <p>
            <label for="type">Category:</label>
            <select id="type" name="categoryId">
                <option value="">All categories</option>
                <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?= h($category->categoryId) ?>"><?= h($category->categoryName) ?></option>
                <?php endforeach; ?>
            </select>
            <output id="type-error" class="field-error-message"></output>
        </p>
    </fieldset>
    
    <fieldset>
        <legend>Topic tags (optional)</legend>
        <div>
            <?php foreach ($tags ?? [] as $tag): ?>
                <p>
                    <input type="checkbox" name="tags[]" id="tag_<?= h($tag->tagId) ?>" value="<?= h($tag->tagId) ?>" />
                    <label for="tag_<?= h($tag->tagId) ?>"><?= h($tag->tag_name) ?></label>
                </p>
            <?php endforeach; ?>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Content</legend>
        <p>
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Write your post here..." required></textarea>
            <output id="description-error" class="field-error-message"></output>
        </p>
        <p>
            <label for="notesFile">Upload your notes:</label>
            <input type="file" name="file" id="notesFile" />
            <output id="notesFile-error" class="field-error-message"></output>
        </p>
    </fieldset>
    
    <p>
        <button type="submit">CREATE POST</button>
    </p>
</form>
</div>
