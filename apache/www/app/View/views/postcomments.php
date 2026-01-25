<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\PostDto $post
 * @var \Unibostu\Dto\CommentWithAuthorDTO[] $comments
 * @var int $userId
 */

$this->extend('main-layout', [
    'title' => 'Unibostu - Post details',
    'courses' => $courses,
    'userId' => $userId,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">',
        '<script type="module" src="/js/comments/main.js"></script>',
        '<script type="module" src="/js/posts/main.js"></script>',
        '<link rel="stylesheet" href="/css/comments.css">',
        ],
    ]);
?>

<a href="/homepage">Go back to homepage</a>

<div class="post_container"> 
    <?php if (!empty($post)): ?>
        <?= $this->component('post', ['post' => $post, 'userId' => $userId]) ?>
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


