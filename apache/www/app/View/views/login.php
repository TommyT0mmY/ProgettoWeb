<?php 
/** @var \Unibostu\Core\RenderingEngine $this */
$this->extend('main-layout', [
    'title' => 'Login Unibostu',
    'additionalHeadCode' => [
        '<script src="js/login.js" defer></script>',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form id="login-form" method="post">
    <fieldset>
        <legend>Login to Unibostu</legend>
        <label for="username">Username:</label>
        <span>
            <input type="text" name="username" id="username" required aria-required="true">
            <span class="field-error-message" id="username-error" aria-live="polite"></span>
        </span>
        <label for="password">Password:</label>
        <span>
            <input type="password" name="password" id="password" required aria-required="true">
            <span class="field-error-message" id="password-error" aria-live="polite"></span>
        </span>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?php echo $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?php echo $csrfKey; ?>">
        <button type="submit" id="login-form_submit">Login</button>
    </fieldset>
</form>

