<?php 
/** 
 * @var \Unibostu\Dto\PostDto $post 
*/ 
?>
<article class="post" data-post-id="<?= htmlspecialchars($post->postId) ?>" data-author-id="<?= htmlspecialchars($post->author->userId) ?>">
    <header>
        <h3><?= htmlspecialchars($post->title) ?></h3>
        <p>
            <em>Posted by <a href="/users/<?= htmlspecialchars($post->author->userId) ?>"><?= htmlspecialchars($post->author->userId) ?></a> on <time datetime="<?= $post->createdAt ?>"><?= $post->createdAt ?></time></em>
        </p>
    </header>
    
    <div class="post-metadata">
        <div class="metadata-section" data-section="community">
            <span class="metadata-label">Corso:</span>
            <ul class="metadata-list community-list">
                <li class="tag subject"><a href="#"><?= htmlspecialchars($post->course->courseName) ?></a></li>
            </ul>
        </div>
        
        <?php if ($post->category): ?>
        <div class="metadata-section" data-section="category">
            <span class="metadata-label">Categoria:</span>
            <ul class="metadata-list category-list">
                <li class="tag type"><a href="#"><?= htmlspecialchars($post->category->categoryName) ?></a></li>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($post->tags)): ?>
        <div class="metadata-section" data-section="tags">
            <span class="metadata-label">Tag:</span>
            <ul class="metadata-list tags-list">
                <?php foreach ($post->tags as $tag): ?>
                <li class="tag topic"><a href="#"><?= htmlspecialchars($tag['tag_name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    
    <p><?= nl2br(htmlspecialchars($post->description)) ?></p>
    
    <footer>
        <ul class="review">
            <?php if ($post->attachmentPath): ?>
                <li>
                    <a href="<?= htmlspecialchars($post->attachmentPath) ?>" download>
                        Download Notes
                    </a>
                </li>
            <?php endif; ?>
                <li class="reaction reaction-like">
                    <button type="button" class="btn-like <?= $post->likedByUser === true ? 'active' : '' ?>" aria-label="Like">
                        <img src="/images/icons/like.svg" alt="" />
                    </button>
                    <data value="<?= $post->likes ?>"><?= $post->likes ?></data>
                </li>
                <li class="reaction reaction-dislike">
                    <button type="button" class="btn-dislike <?= $post->likedByUser === false ? 'active' : '' ?>" aria-label="Dislike">
                        <img src="/images/icons/dislike.svg" alt="" />
                    </button>
                    <data value="<?= $post->dislikes ?>"><?= $post->dislikes ?></data>
                </li>
                <?php if (isset($commentsButton) ? $commentsButton : true): ?>
                <li>
                    <a href="/posts/<?= htmlspecialchars($post->postId) ?>" aria-label="Go to post comments">Comments</a>
                </li>
                <?php endif; ?>
        </ul>            
   </footer>
</article>

