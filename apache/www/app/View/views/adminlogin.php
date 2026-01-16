<?php 
/** @var \Unibostu\Core\RenderingEngine $this */
$this->extend('main-layout', [
    'title' => 'Administrator Login',
    'additionalHeadCode' => [
        '<script type="module" src="js/adminlogin.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>

<form class="fullscreen-form" id="adminlogin-form" method="post" novalidate>
    <fieldset>
        <legend>Administrator Login</legend>
        <output class="form-error-message" for="username password" role="alert"></output>
        <div class="field-holder">
            <input type="text" name="username" id="username" aria-describedby="username-error" required>
            <label for="username">Username</label>
            <output class="field-error-message" id="username-error" for="username"></output>
        </div>
        <div class="field-holder">
            <input type="password" name="password" id="password" aria-describedby="password-error" minlength="6" required>
            <label for="password">Password</label>
            <output class="field-error-message" id="password-error" for="password"></output>
        </div>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>">
        <button type="submit" id="adminlogin-form_submit" disabled>Login</button>
        <p>Are you not an administrator? <a href="/login">Login here</a>.</p>
    </fieldset>
</form>

