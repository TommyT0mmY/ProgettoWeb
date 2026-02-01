<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\FacultyDTO[] $faculties
 */

$this->extend('loggedout-layout', [
    'title' => 'Register Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/register.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form class="fullscreen-form" id="register-form" method="post" novalidate>
    <fieldset>
        <legend>Register to Unibostu</legend>
        <output class="form-error-message" for="username firstname lastname password facultyid" role="alert"></output>
        <div class="field-holder">
            <input type="text" name="username" id="username" aria-describedby="username-error" required>
            <label for="username">Username</label>
            <output class="field-error-message" id="username-error" for="username"></output>
        </div>
        <div class="field-holder">
            <input type="text" name="firstname" id="firstname" aria-describedby="firstname-error" required>
            <label for="firstname">First Name</label>
            <output class="field-error-message" id="firstname-error" for="firstname"></output>
        </div>
        <div class="field-holder">
            <input type="text" name="lastname" id="lastname" aria-describedby="lastname-error" required>
            <label for="lastname">Last Name</label>
            <output class="field-error-message" id="lastname-error" for="lastname"></output>
        </div>
        <div class="field-holder">
            <input type="password" name="password" id="password" aria-describedby="password-error" minlength="6" required>
            <label for="password">Password</label>
            <output class="field-error-message" id="password-error" for="password"></output>
        </div>
        <div class="field-holder">
            <select name="facultyid" id="facultyid" aria-describedby="facultyid-error" required>
                <option value="" disabled selected>Select your faculty</option>
                <?php foreach ($faculties as $faculty): ?>
                <option value="<?= h($faculty->facultyId); ?>"><?= h($faculty->facultyName); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="facultyid">Faculty ID</label>
            <output class="field-error-message" id="facultyid-error" for="facultyid"></output>
        </div>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>">
        <button type="submit" id="register-form_submit" disabled>Register</button>
        <p>Already have an account? <a href="/login">Login here</a>.</p>
    </fieldset>
</form>
