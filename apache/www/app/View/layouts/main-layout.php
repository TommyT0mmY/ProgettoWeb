<?php
/** 
 * @var \Unibostu\Core\RenderingEngine $this 
 * @var string|null $title
 * @var string $content
 * @var array<string> $additionalHeadCode
 * @var string $userId
 * @var array<Unibostu\Model\DTO\CourseDTO> $courses
 */

/**
 * This layout is intended for the pages of the main application after user authentication.
 * Do not use it for public pages (use 'loggedout-layout' instead) and administration pages
 * (use 'admin-layout' instead).
 *
 * Views using this layout must provide:
 * - $title: The page title;
 * - $additionalHeadCode: An array of strings containing additional HTML code to be included in the head section;
 * - $userId: The ID of the current user;
 * - $courses: An array of CourseDTO objects representing the user's courses;
 *
 * Note that $content is automatically provided by the rendering engine.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?? 'Unibostu' ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /> 
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="/css/base.css" />
    <link rel="stylesheet" href="/css/popup.css" />
    <script type="module" src="/js/navbar-css.js"></script>
    <link rel="stylesheet" href="/css/style.css">
    <?php if (!empty($additionalHeadCode)): ?>
        <?php foreach ($additionalHeadCode as $code): ?>
            <?= $code ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?= $this->component('header', ['userId' => $userId]) ?>
    <div class="containerNavMain">
        <?= $this->component('sidebar', ['courses' => $courses]) ?>
        <main>
            <?= $content ?>
        </main>
    </div>
    <div id="overlay"></div>
    <!-- Popup Template -->
    <template id="popup-template">
        <section class="popup" aria-label="Popup di errore">
          <div class="popup__content">
              <span class="popup__icon error-icon" aria-hidden="true"></span>
              <p class="popup__text"></p>
              <button type="button" aria-label="Chiudi popup" class="popup__icon popup__close"></button>
          </div>
        </section>
    </template>

    <!-- Post Template -->
    <template id="post-template">
        <article class="Post" data-post-id="" data-author-id="">
            <header>
                <h3 data-field="title"></h3>
                <p>
                    <em>Posted by <span data-field="author"></span> on <time data-field="createdAt" datetime=""></time></em>
                </p>
            </header>
            
            <ul class="tags" data-field="tags">
                <li class="tag subject"><a href="#" data-field="courseName"></a></li>
            </ul>
            
            <p data-field="description"></p>
            
            <footer>
                <ul class="review" data-field="reviewList">
                    <li class="reaction reaction-like">
                        <button type="button" class="btn-like" aria-label="Like">
                            <img src="/images/icons/like.svg" alt="" />
                        </button>
                        <data value="0" data-field="likes">0</data>
                    </li>
                    <li class="reaction reaction-dislike">
                        <button type="button" class="btn-dislike" aria-label="Dislike">
                            <img src="/images/icons/dislike.svg" alt="" />
                        </button>
                        <data value="0" data-field="dislikes">0</data>
                    </li>
                    <li>
                        <a href="" data-field="commentsLink" aria-label="Go to post comments">Comments</a>
                    </li>
                </ul>            
            </footer>
        </article>
    </template>

    <?php ['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true); ?>
    <script>
        window.currentUser = '<?= isset($userId) ? htmlspecialchars($userId) : '' ?>';
        window.csrfToken = '<?= htmlspecialchars($csrfToken) ?>';
        window.csrfKey = '<?= htmlspecialchars($csrfKey) ?>';
    </script>
</body>
</html>
