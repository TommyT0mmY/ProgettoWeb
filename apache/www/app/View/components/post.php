<?php 
/** 
 * @var ?\Unibostu\Model\DTO\PostDTO $post If null, a default post without data is provided
 * @var bool $forAdmin Indicates if the post is being rendered in an admin context
 * @var string $currentPageUrl The current page URL to use for category/tag links (e.g., '/', '/courses/123', '/users/abc')
*/ 

$postId = $post->postId ?? '';
$authorId = $post->author->userId ?? '';
$createdAt = $post->createdAt ?? '';
$title = $post->title ?? 'Untitled Post';
$categoryId = $post->category->categoryId ?? '';
$categoryName = $post->category->categoryName ?? '';
$courseId = $post->course->courseId ?? '';
$courseName = $post->course->courseName ?? '';
$description = $post->description ?? '';
$tags = $post->tags ?? [];
$attachmentPath = $post->attachmentPath ?? '';
$likes = $post->likes ?? 0;
$dislikes = $post->dislikes ?? 0;
$likedByUser = $post->likedByUser ?? null;
$courseLink = $courseId ? "/courses/{$courseId}" : '#';
$categoryLink = $categoryId ? ($currentPageUrl ?? '/') . "?categoryId={$categoryId}" : '#';
?>
<article class="post" data-post-id="<?=h($postId)?>" data-author-id="<?=h($authorId)?>" data-course-id="<?=h($courseId)?>">
    <header>
        <h3 data-field="title"><?= h($title) ?></h3>
        <p>
            <em>Posted by <a data-field="author" href="/users/<?=h($authorId)?>"><?=h($authorId)?></a> on <time data-field="createdAt" datetime="<?=$createdAt?>"><?=$createdAt?></time></em>
        </p>
    </header>
    
    <div class="post-metadata">
        <div class="metadata-section" data-section="course">
            <span class="metadata-label">Course:</span>
            <ul class="metadata-list community-list">
                <li class="tag subject"><a href="<?= h($courseLink) ?>" data-field="courseName" data-course-id="<?= h($courseId) ?>"><?= h($courseName) ?></a></li>
            </ul>
        </div>
        
        <div class="metadata-section" data-section="category" style="display: <?= $categoryName ? '' : 'none' ?>;">
            <span class="metadata-label">Category:</span>
            <ul class="metadata-list category-list" data-field="category">
                <?php if ($categoryName): ?>
                <li class="tag type"><a href="<?= h($categoryLink) ?>" data-category-id="<?= h($categoryId) ?>"><?= h($categoryName) ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="metadata-section" data-section="tags" style="display: <?= !empty($tags) ? '' : 'none' ?>;">
            <span class="metadata-label">Tag:</span>
            <ul class="metadata-list" data-field="tags">
                <?php foreach ($tags as $tag): ?>
                <li class="tag topic"><a href="<?= $courseId ? '/courses/' . h($courseId) . '?tags[]=' . h($tag['tag_id']) : '#' ?>" data-tag-id="<?= h($tag['tag_id']) ?>"><?= h($tag['tag_name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <p data-field="description"><?= nl2br(h($description)) ?></p>
    <footer>
        <ul class="review" data-field="reviewList">
            <?php if ($attachmentPath): ?>
            <li><a href="<?=h($attachmentPath)?>" download>Download Notes</a></li>
            <?php endif; ?>
            <li class="reaction reaction-like <?= $forAdmin ? 'disabled' : '' ?>">
                <button type="button" class="btn-like <?= $likedByUser === true ? 'active' : '' ?>" <?= $forAdmin ? 'disabled' : '' ?> aria-label="Like this post">
                    <img src="/images/icons/like.svg" alt="">
                    <data data-field="likes" value="<?= $likes ?>"><?= $likes ?></data>
                </button>
            </li>
            <li class="reaction reaction-dislike <?= $forAdmin ? 'disabled' : '' ?>">
                <button type="button" class="btn-dislike <?= $likedByUser === false ? 'active' : '' ?>" <?= $forAdmin ? 'disabled' : '' ?> aria-label="Dislike this post">
                    <img src="/images/icons/dislike.svg" alt="">
                    <data data-field="dislikes" value="<?= $dislikes ?>"><?= $dislikes ?></data>
                </button>
            </li>
            <?php if (isset($commentsButton) ? $commentsButton : true): ?>
            <li>
                <a data-field="commentsLink" href="/posts/<?= h($postId) ?>" aria-label="Go to post comments">Comments</a>
            </li>
            <?php endif; ?>
        </ul>            
   </footer>
</article>

