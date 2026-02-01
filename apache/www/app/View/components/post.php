<?php 
/** 
 * @var ?\Unibostu\Model\DTO\PostDTO $post If null, a default post without data is provided
 * @var bool $forAdmin Indicates if the post is being rendered in an admin context
*/ 

$postId = $post->postId ?? '';
$authorId = $post->author->userId ?? '';
$createdAt = $post->createdAt ?? '';
$title = $post->title ?? 'Untitled Post';
$categoryName = $post->category->categoryName ?? '';
$courseName = $post->course->courseName ?? '';
$description = $post->description ?? '';
$tags = $post->tags ?? [];
$attachmentPath = $post->attachmentPath ?? '';
$likes = $post->likes ?? 0;
$dislikes = $post->dislikes ?? 0;
$likedByUser = $post->likedByUser ?? null;
$commentsLink = $postId ? "/posts/{$postId}" : '#';
?>
<article class="post" data-post-id="<?=h($postId)?>" data-author-id="<?=h($authorId)?>">
    <header>
        <h3 data-field="title"><?= h($post->title) ?></h3>
        <p>
            <em>Posted by <a data-field="author" href="<?=h($authorId)?>"><?=h($authorId)?></a> on <time data-field="createdAt" datetime="<?=$createdAt?>"><?=$createdAt?></time></em>
        </p>
    </header>
    
    <div class="post-metadata">
        <div class="metadata-section" data-section="course">
            <span class="metadata-label">Course:</span>
            <ul class="metadata-list community-list">
                <li class="tag subject"><a href="#" data-field="courseName"><?= h($courseName) ?></a></li>
            </ul>
        </div>
        
        <div class="metadata-section" data-section="category" style="display: none;">
            <span class="metadata-label">Category:</span>
            <ul class="metadata-list category-list" data-field="category">
                <?php if ($categoryName): ?>
                <li class="tag type"><a href="#"><?= h($categoryName) ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <?php if (!$post || ($post && !empty($tags))): ?>
        <div class="metadata-section" data-section="tags" style="display: none;">
            <span class="metadata-label">Tag:</span>
            <ul class="metadata-list tags-list" data-field="tags">
                <?php foreach ($tags as $tag): ?>
                <li class="tag topic"><a href="#"><?= h($tag['tag_name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <p data-field="description"><?= nl2br(h($post->description)) ?></p>
    <footer>
        <ul class="review" data-field="reviewList">
            <?php if ($attachmentPath): ?>
            <li><a href="<?=h($attachmentPath)?>" download>Download Notes</a></li>
            <?php endif; ?>
            <li class="reaction reaction-like">
                <button type="button" class="btn-like <?= $likedByUser === true ? 'active' : '' ?>" aria-label="Like">
                    <img src="/images/icons/like.svg" alt="Like icon">
                </button>
                <data data-field="likes" value="<?= $likes ?? 0 ?>"><?= $likes ?? 0 ?></data>
            </li>
            <li class="reaction reaction-dislike">
                <button type="button" class="btn-dislike <?= $likedByUser === false ? 'active' : '' ?>" aria-label="Dislike">
                    <img src="/images/icons/dislike.svg" alt="Dislike icon" />
                </button>
                <data data-field="dislikes" value="<?= $dislikes ?? 0 ?>"><?= $dislikes ?? 0 ?></data>
            </li>
            <?php if (isset($commentsButton) ? $commentsButton : true): ?>
            <li>
                <a data-field="commentsLink" href="/posts/<?= h($postId) ?>" aria-label="Go to post comments">Comments</a>
            </li>
            <?php endif; ?>
        </ul>            
   </footer>
</article>

