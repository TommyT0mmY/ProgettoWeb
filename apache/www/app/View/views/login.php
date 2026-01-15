<?php 
/** @var \Unibostu\Core\RenderingEngine $this */
$this->extend('main-layout', [
    'title' => 'Login Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="js/login.js"></script>',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>

<form id="login-form" method="post" novalidate>
    <fieldset>
        <legend>Login to Unibostu</legend>
        <output class="form-error-message" for="username password" role="alert"></output>
        <label for="username">Username:</label>
        <span>
            <input type="text" name="username" id="username" aria-describedby="username-error" required>
            <output class="field-error-message" id="username-error" for="username"></output>
        </span>
        <label for="password">Password:</label>
        <span>
            <input type="password" name="password" id="password" aria-describedby="password-error" minlength="6" required>
            <output class="field-error-message" id="password-error" for="password"></output>
        </span>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>">
        <button type="submit" id="login-form_submit">Login</button>
    </fieldset>
</form>

