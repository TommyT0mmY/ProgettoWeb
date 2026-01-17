<!DOCTYPE html>
<html lang="it">
<head>
    <title><?= $title ?? 'Default' ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /> 
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" media="print" onload="this.media='all'" />

    <?php if (!empty($additionalHeadCode)): ?>
        <?php foreach ($additionalHeadCode as $code): ?>
            <?= $code ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?= $this->component('header') ?>
    <?= $this->component('sidebar', ['courses' => $courses]) ?>
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
</body>
</html>
