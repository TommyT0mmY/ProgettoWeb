<?php
declare(strict_types=1);


// TODO

/** 
 * @var \Unibostu\Core\RenderingEngine $this 
 * @var string|null $title
 * @var string $content
 * @var array<string> $additionalHeadCode
 * @var string $userId
 * @var array<Unibostu\Model\DTO\CourseDTO> $courses
 */

/**
 * This layout is intended for the  administration pages
 * Do not use it for public pages (use 'loggedout-layout' instead) and main application pages.
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
    <link rel="stylesheet" href="/css/style.css">
    <?php if (!empty($additionalHeadCode)): ?>
        <?php foreach ($additionalHeadCode as $code): ?>
            <?= $code ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?= $this->component('admin-header') ?>
    <main>
        <?= $content ?>
    </main>
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
        <?=$this->component("post", ["post" => null, "forAdmin" => true])?>
    </template>

    <?php ['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true); ?>
    <script>
        window.currentUser = '<?= isset($userId) ? h($userId) : '' ?>';
        window.isAdmin = true;
        window.csrfToken = '<?= h($csrfToken) ?>';
        window.csrfKey = '<?= h($csrfKey) ?>';
    </script>
</body>
</html>

