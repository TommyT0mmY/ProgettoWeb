<?php 
/** 
 * @var \Unibostu\Core\RenderingEngine $this 
 * @var array $subscribedCourses 
 * @var array<Unibostu\Model\DTO\FacultyDTO> $faculties
 */
$this->extend('loggedout-layout', [
    'title' => 'Select Courses - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/select-courses.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>

<form class="fullscreen-form" id="select-courses-form" method="post" novalidate>
    <fieldset>
        <legend>Legend</legend>
        <output class="form-error-message" for="username password" role="alert"></output>
        <div class="field-holder">
            <input type="text" name="username" id="username" aria-describedby="username-error" required />
            <label for="username">Username</label>
            <output class="field-error-message" id="username-error" for="username"></output>
        </div>
        <div class="field-holder">
            <input type="password" name="password" id="password" aria-describedby="password-error" minlength="6" required />
            <label for="password">Password</label>
            <output class="field-error-message" id="password-error" for="password"></output>
        </div>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>" />
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>" />
        <button type="submit" id="select-courses-submit" disabled>Save Preferences</button>
    </fieldset>
</form>

