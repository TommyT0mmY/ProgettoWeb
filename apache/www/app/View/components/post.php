<?php /** @var \Unibostu\Dto\PostDto $post */ ?>
<article class="post">
    <h2><?= htmlspecialchars($post->title) ?></h2>
    <p><?= nl2br(htmlspecialchars($post->content)) ?></p>
    <p><em>Posted by <?= htmlspecialchars($post->author) ?> on <?= $post->createdAt->format('Y-m-d H:i') ?></em></p>
</article>
