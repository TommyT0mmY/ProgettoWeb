<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var FacultyDTO[] $faculties
 */

use Unibostu\Model\DTO\FacultyDTO;

$this->extend('main-layout', [
    'title' => 'Register Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="js/register.js"></script>',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form id="register-form" method="post" novalidate>
    <fieldset>
        <legend>Register to Unibostu</legend>
        <output class="form-error-message" for="username firstname lastname password facultyid" role="alert"></output>
        <label for="username">Username:</label>
        <span>
            <input type="text" name="username" id="username" aria-describedby="username-error" required>
            <output class="field-error-message" id="username-error" for="username"></output>
        </span>
        <label for="firstname">First Name:</label>
        <span>
            <input type="text" name="firstname" id="firstname" aria-describedby="firstname-error" required>
            <output class="field-error-message" id="firstname-error" for="firstname"></output>
        </span>
        <label for="lastname">Last Name:</label>
        <span>
            <input type="text" name="lastname" id="lastname" aria-describedby="lastname-error" required>
            <output class="field-error-message" id="lastname-error" for="lastname"></output>
        </span>
        <label for="password">Password:</label>
        <span>
            <input type="password" name="password" id="password" aria-describedby="password-error" minlength="6" required>
            <output class="field-error-message" id="password-error" for="password"></output>
        </span>
        <label for="facultyid">Faculty ID:</label>
        <span>
            <select name="facultyid" id="facultyid" aria-describedby="facultyid-error" required>
                <option value="" disabled selected>Select your faculty</option>
                <?php foreach ($faculties as $faculty): ?>
                <option value="<?= htmlspecialchars($faculty->facultyId); ?>"><?= htmlspecialchars($faculty->facultyName); ?></option>
                <?php endforeach; ?>
            </select>
            <output class="field-error-message" id="facultyid-error" for="facultyid"></output>
        </span>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>">
        <button type="submit" id="register-form_submit" disabled>Register</button>
    </fieldset>
</form>
