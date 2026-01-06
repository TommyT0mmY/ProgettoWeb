<?php /** @var \Unibostu\Dto\PostDto $post */ ?>
<article class="post">
    <h2><?= htmlspecialchars($post->title) ?></h2>
    <p><?= nl2br(htmlspecialchars($post->description)) ?></p>
    <p><em>Posted by <?= htmlspecialchars($post->author->userId) ?> on <?= $post->createdAt ?></em></p>
    <p>Course <?= htmlspecialchars($post->course->courseName) ?></p>
    <?php if (!empty($post->tags)): ?>
        <p>Tags:
            <?php foreach ($post->tags as $tag): ?>
                <span class="tag"><?= htmlspecialchars($tag['tag_name']) ?></span>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>
    <?php if ($post->category): ?>
        <p>Category: <span class="category"><?= htmlspecialchars($post->category->categoryName) ?></span></p>
    <?php endif; ?>
</article>
