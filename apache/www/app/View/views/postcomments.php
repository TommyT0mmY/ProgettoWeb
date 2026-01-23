<?php 
/** 
 * @var \Unibostu\core\RenderingEngine $this
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\PostDto $post
 * @var \Unibostu\Dto\CommentWithAuthorDTO[] $comments
 */
//devo cambiare nome a sto file
$this->extend('main-layout', [
    'title' => 'Unibostu - Post details',
    'courses' => $courses,
    'userId' => $userId,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">',
        '<script type="module" src="/js/commentsTree/main.js"></script>',
        '<link rel="stylesheet" href="/css/comments.css">',
        ],
    ]);
?>

<a href="/homepage">Go back to homepage</a>

<div class="post_container"> 
    <?php if (!empty($post)): ?>
        <?= $this->component('post', ['post' => $post]) ?>
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
                <button type="button" class="btn-reply">
                    Rispondi
                </button>
            </div>
            
            <div class="comment-replies"></div>
        </article>
    </template>

    <template id="comment-form-template">
        <form class="comment-form" novalidate>
            <div class="form-group">
                <textarea 
                    class="comment-input" 
                    placeholder="Scrivi un commento..." 
                    rows="4" 
                    required
                    maxlength="1000"
                ></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    Commenta
                </button>
                <button type="button" class="btn-cancel">Annulla</button>
            </div>
        </form>
    </template>
</div>


