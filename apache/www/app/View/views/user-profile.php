<?php
$this->extend('main-layout', [
    'title' => 'Unibostu - User Profile',
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
        ],
    ]);
?>

<div class="container">  

    <section>
    <h2>Personal details</h2>    
        <div class="profile"><?= htmlspecialchars(substr($user->firstName ?? '', 0, 1) . '.' . substr($user->lastName ?? '', 0, 1)); ?></div>
        <p>Username: <strong><?= htmlspecialchars($user->userId); ?></strong></p>
        <p>Name: <strong><?= htmlspecialchars($user->firstName ?? ''); ?></strong></p>
        <p>Last name: <strong><?= htmlspecialchars($user->lastName ?? ''); ?></strong></p>
        <button type="button">Change info</button>   
    </section>

    <section>
    <h2>University details</h2>         
        <p>Faculty: <strong><?= htmlspecialchars($faculty->facultyName ?? ''); ?></strong></p> 
        <p>Chosen courses:</p>                              
            <?php if (!empty($courses)): ?>               
                <ul class="tags">
                    <?php foreach ($courses as $course): ?>
                    <li class="tag subject"><a href="#"><?= htmlspecialchars($course->courseName) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>       
            <a href="/studentpreferences">Change chosen courses</a>         
    </section>

</div>

<hr/>

<section>

    <header>
    <h2> My posts </h2>
    </header>

    <section class="post-filters"> 

        <form action="/courses/<?= htmlspecialchars($thisCourse->courseId) ?>" method="GET" id="filter-form">

            <label for="filter-type">Category</label>
            <select id="filter-type" name="categoryId">
            <option  value="">All categories</option>
            <?php foreach ($categories ?? [] as $category): ?>
                <option value="<?= htmlspecialchars($category->categoryId) ?>"><?= htmlspecialchars($category->categoryName) ?></option>
            <?php endforeach; ?>
            </select>

            <label for="ordering">Order by date</label>
            <select id="ordering" name="sortOrder">
                <option id="tag_sorting" value="desc">Newest post first</option>
                <option id="tag_sorting" value="asc">Oldest post first</option>
            </select>

            <input type="submit" value="Filter"/>

        </form>

    </section>

    <hr/>

    <div class="post_container">
    <?php foreach ($posts ?? [] as $post): ?>
        <?= $this->component('post', ['post' => $post]) ?>
    <?php endforeach; ?>
    </div>

</section>