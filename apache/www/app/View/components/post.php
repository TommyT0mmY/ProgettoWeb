<?php 
/** 
 * @var \Unibostu\Dto\PostDto $post 
*/ 
?>
<article class="Post" data-post-id="<?= htmlspecialchars($post->postId) ?>" data-author-id="<?= htmlspecialchars($post->author->userId) ?>">
    <header>
        <h3><?= htmlspecialchars($post->title) ?></h3>
    </header>
    <p><em>Posted by <?= htmlspecialchars($post->author->userId) ?> on <?= $post->createdAt ?></em></p>
    
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
                    <button class="btn-like <?= $post->likedByUser === true ? 'active' : '' ?>">
                        <img src="images/icons/like.svg" alt="like">
                    </button>
                    <data value="<?= $post->likes ?>"><?= $post->likes ?></data>
                </li>
                <li class="reaction reaction-dislike">
                    <button class="btn-dislike <?= $post->likedByUser === false ? 'active' : '' ?>">
                        <img src="images/icons/dislike.svg" alt="dislike">
                    </button>
                    <data value="<?= $post->dislikes ?>"><?= $post->dislikes ?></data>
                </li>
                <li>
                    <a href="comments.php">Comments</a> <!--to be updated-->
                </li>
        </ul>            
   </footer>
</article>

