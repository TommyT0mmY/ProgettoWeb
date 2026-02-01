<?php 
/** 
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\CourseDTO[] $courses User's subscribed courses (only for non-admin users)
 * @var \Unibostu\Model\DTO\PostDTO $post The post being viewed
 * @var \Unibostu\Model\DTO\CommentWithAuthorDTO[] $comments Comments on the post
 * @var string $userId Current user ID
 * @var bool $isAdmin Whether the current user is an admin
 */

// Use different layouts based on user role
$layout = $isAdmin ? 'admin-layout' : 'main-layout';
$layoutParams = [
    'title' => 'Unibostu - Post details',
    'additionalHeadCode' => [
        '<script type="module" src="/js/posts/single-post.js"></script>',
        '<script type="module" src="/js/comments/main.js"></script>',
        '<link rel="stylesheet" href="/css/comments.css">',
    ],
    'userId' => $userId,
];

// Add courses only for non-admin users (main-layout requires it)
if (!$isAdmin) {
    $layoutParams['courses'] = $courses;
}

$this->extend($layout, $layoutParams);
?>

<a href="/">Go back to homepage</a>

<div class="post-container"> 
    <?php if (!empty($post)): ?>
        <?= $this->component('post', ['post' => $post, 'commentsButton' => false, 'forAdmin' => $isAdmin, 'currentPageUrl' => "/posts/{$post->postId}"]) ?>
    <?php else: ?>
        <p>Post not found.</p>
    <?php endif; ?>
    
    <section id="comments-section" class="comments-section" data-post-id="<?= $post->postId ?>">
        
    </section>

    <template id="comment-template">
        <article class="comment">
            <div class="comment-header">
                <div class="comment-author-info">
                    <span class="comment-author-name"></span>
                    <time class="comment-date"></time>
                </div>
            </div>
            
            <div class="comment-body">
                <p class="comment-text"></p>
            </div>
            
            <div class="comment-actions">
                <button type="button" class="btn-reply" aria-label="Reply to comment">
                    Reply
                </button>
            </div>
            
            <div class="comment-replies"></div>
        </article>
    </template>

    <template id="comment-form-template">
        <form class="comment-form" novalidate>
            <div class="form-group">
                <label for="comment-input" class="sr-only">Write a comment</label>
                <textarea
                    id="comment-input"
                    class="comment-input" 
                    placeholder="Write a comment..." 
                    rows="4" 
                    required
                    maxlength="1000"
                ></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    Submit
                </button>
                <button type="button" class="btn-cancel" aria-label="Cancel comment">Cancel</button>
            </div>
        </form>
    </template>
</div>


