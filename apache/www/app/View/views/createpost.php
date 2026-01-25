 <?php 
/** 
 * @var \Unibostu\Dto\CategoryDto[] $categories
 * @var \Unibostu\Dto\TagDto[] $tags
 * @var \Unibostu\Dto\CourseDto $thisCourse
 * @var \Unibostu\Dto\CourseDto[] $courses
 */

$this->extend('main-layout', ['title' => 'Unibostu - Create post',
     'courses' => $courses ,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
        ] ]
    ); 
?>
 
<h2 class="createPost">Create post for: <?= htmlspecialchars($thisCourse->courseName) ?></h2>

<form action="#" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Post information</legend>
        <p>
            <label for="postTitle">Title:</label>
            <input type="text" name="title" id="postTitle" placeholder="Insert post title" required />
        </p>
        <p>
            <label for="type">Category:</label>
            <select id="type" name="categoryId">
                <option value="">All categories</option>
                <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?= htmlspecialchars($category->categoryId) ?>"><?= htmlspecialchars($category->categoryName) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    </fieldset>
    
    <fieldset>
        <legend>Topic tags (optional)</legend>
        <div>
            <?php foreach ($tags ?? [] as $tag): ?>
                <p>
                    <input type="checkbox" name="tag" id="tag_<?= htmlspecialchars($tag->tagId) ?>" value="<?= htmlspecialchars($tag->tagId) ?>" />
                    <label for="tag_<?= htmlspecialchars($tag->tagId) ?>"><?= htmlspecialchars($tag->tag_name) ?></label>
                </p>
            <?php endforeach; ?>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Content</legend>
        <p>
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Write your post here..." required></textarea>
        </p>
        <p>
            <label for="notesFile">Upload your notes:</label>
            <input type="file" name="file" id="notesFile" />
        </p>
    </fieldset>
    
    <p>
        <input type="submit" value="CREATE POST" />
    </p>
</form>