<?php 
/** 
 * @var \Unibostu\Dto\PostDto $post 
*/ 
?>
<article class="Post" data-post-id="<?= htmlspecialchars($post->postId) ?>" data-author-id="<?= htmlspecialchars($post->author->userId) ?>">
    <header>
        <h3><?= htmlspecialchars($post->title) ?></h3>
        <p>
            <em>Posted by <?= htmlspecialchars($post->author->userId) ?> on <time datetime="<?= $post->createdAt ?>"><?= $post->createdAt ?></time></em>
        </p>
    </header>
    
    <ul class="tags">
     <li class="tag subject"><a href="#"><?= htmlspecialchars($post->course->courseName) ?></a></li>
     <?php if ($post->category): ?>
     <li class="tag type"><a href="#"><?= htmlspecialchars($post->category->categoryName) ?></a></li>
     <?php endif; ?><!--dunque la categoria potrebbe non esserci-->
      <?php if (!empty($post->tags)): ?>
       <?php foreach ($post->tags as $tag): ?>
        <li class="tag topic"><a href="#"><?= htmlspecialchars($tag['tag_name']) ?></a></li>
       <?php endforeach; ?>
     <?php endif; ?>
      <!--la cosa è che avevo messo a href cosi potevi cliccare sui tag, magari per il corso e per la categoria lo tolgo?
      ma mi sembra adatta ai tag del corso per poterli filtrare in base a quello, boh non so-->   
    </ul>

    
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

