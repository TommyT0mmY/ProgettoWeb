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
    'additionalHeadCode' => [
        '<script type="module" src="js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
        ],
    ]);
?>

<div class="post_container"> 
    <?php if (!empty($post)): ?>
        <?= $this->component('post', ['post' => $post]) ?>
    <?php else: ?>
        <p>Post not found.</p>
    <?php endif; ?>
    <section class="comments-section">
        <section class="Post comment"> 
            <header>
                <h3>Add a Comment</h3>
            </header>
            <form id="comment-form" action="/posts/<?= htmlspecialchars($post->postId) ?>/comments/addComment" method="POST">
                <textarea name="comment-text" id="comment-text" placeholder="Write your comment here..." required></textarea>
                <button type="submit">Submit</button>
            </form>
        </section>

        <header>
            <h3>Comments</h3>
        </header>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <article class="comment" data-commentId="<?= htmlspecialchars($comment->commentId) ?>">
                    <header>
                        <h4><?= htmlspecialchars($comment->author->userId) ?></h4>
                        <p><em>Posted on <?= $comment->createdAt ?></em></p>
                    </header>
                    <p><?= nl2br(htmlspecialchars($comment->text)) ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </section>
</div>


