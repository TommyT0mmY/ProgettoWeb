<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 */

$this->extend('loggedout-layout', [
    'title' => 'Change Password - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/change-password.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form class="fullscreen-form" id="change-password-form" method="post" novalidate>
    <fieldset>
        <legend>Change Password</legend>
        <output class="form-error-message" for="currentpassword newpassword confirmpassword" role="alert"></output>
        
        <div class="field-holder">
            <input type="password" name="currentpassword" id="currentpassword" aria-describedby="currentpassword-error" required>
            <label for="currentpassword">Current Password</label>
            <output class="field-error-message" id="currentpassword-error" for="currentpassword"></output>
        </div>
        
        <div class="field-holder">
            <input type="password" name="newpassword" id="newpassword" aria-describedby="newpassword-error" minlength="6" required>
            <label for="newpassword">New Password</label>
            <output class="field-error-message" id="newpassword-error" for="newpassword"></output>
        </div>
        
        <div class="field-holder">
            <input type="password" name="confirmpassword" id="confirmpassword" aria-describedby="confirmpassword-error" minlength="6" required>
            <label for="confirmpassword">Confirm New Password</label>
            <output class="field-error-message" id="confirmpassword-error" for="confirmpassword"></output>
        </div>
        
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>">
        
        <output class="form-status-message" role="status"></output>
        
        <div class="controls-container">
            <button type="button" onclick="history.back();">Back</button>
            <button type="submit" id="change-password-submit" disabled>Change Password</button>
        </div>
    </fieldset>
</form>
