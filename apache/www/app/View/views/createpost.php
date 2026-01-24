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
 
<h2 class="createPost"><?= htmlspecialchars($thisCourse->courseName) ?></h2>

<form action="#" method="post" enctype="multipart/form-data">
    <ul>
        <li>
            <label for="postTitle" id="labelPostTitle">Title:</label>
            <input type="text" name="title" id="postTitle" placeholder="Insert post title" required/>
        </li>
        <li>
            <label for="type">Category</label>
            <select id="type" name="categoryId">
            <option  value="">All categories</option>
            <?php foreach ($categories ?? [] as $category): ?>
                <option value="<?= htmlspecialchars($category->categoryId) ?>"><?= htmlspecialchars($category->categoryName) ?></option>
            <?php endforeach; ?>
            </select>
        </li>
        <li> 
            <fieldset>
                <legend>Topic tags (optional)</legend>
                <div>
                    <?php foreach ($tags ?? [] as $tag): ?>
                        <label> <input type="checkbox" name="tag" value="<?= htmlspecialchars($tag->tagId) ?>"/> <?= htmlspecialchars($tag->tag_name) ?></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </li>
        <li>
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Write your post here..." required>       
            </textarea>
        </li>
        <li>
            <label for="notes">Upload your notes:</label>
            <input type="file" name="file" id="notesFile" />      
        </li>
        <li>
            <input type="submit" value="CREATE POST" />
        </li>
    </ul>
</form>