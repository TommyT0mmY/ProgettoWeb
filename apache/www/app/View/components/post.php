<?php /** @var \Unibostu\Dto\PostDto $post */ ?>
<article class="post">
    <h2><?= htmlspecialchars($post->title) ?></h2>
    <p><?= nl2br(htmlspecialchars($post->description)) ?></p>
    <p><em>Posted by <?= htmlspecialchars($post->author->userId) ?> on <?= $post->createdAt ?></em></p>
</article>
