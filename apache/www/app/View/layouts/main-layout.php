<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Default' ?></title>
    <link rel="stylesheet" href="/css/popup.css">
    <link rel="stylesheet" href="/css/style2.css"><!-- Main stylesheet provvisorio #Aya -->
    
    <script src="js/popup.js" defer></script>
    <?php if (!empty($additionalHeadCode)): ?>
        <?php foreach ($additionalHeadCode as $code): ?>
            <?= $code ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?= $this->component('header') ?> <!--non so se va bene cosi #Aya-->
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
