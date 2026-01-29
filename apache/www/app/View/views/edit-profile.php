<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\UserDTO $user
 * @var \Unibostu\Model\DTO\FacultyDTO[] $faculties
 * @var string $userId
 */

$this->extend('loggedout-layout', [
    'title' => 'Edit Profile - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/edit-profile.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form class="fullscreen-form" id="edit-profile-form" method="post" novalidate>
    <fieldset>
        <legend>Edit Profile</legend>
        <output class="form-error-message" for="firstname lastname facultyid" role="alert"></output>
        
        <div class="field-holder">
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user->userId); ?>" disabled>
            <label for="username">Username</label>
        </div>
        
        <div class="field-holder">
            <input type="text" name="firstname" id="firstname" aria-describedby="firstname-error" value="<?= htmlspecialchars($user->firstName ?? ''); ?>" required>
            <label for="firstname">First Name</label>
            <output class="field-error-message" id="firstname-error" for="firstname"></output>
        </div>
        
        <div class="field-holder">
            <input type="text" name="lastname" id="lastname" aria-describedby="lastname-error" value="<?= htmlspecialchars($user->lastName ?? ''); ?>" required>
            <label for="lastname">Last Name</label>
            <output class="field-error-message" id="lastname-error" for="lastname"></output>
        </div>
        
        <div class="field-holder">
            <select name="facultyid" id="facultyid" aria-describedby="facultyid-error" required>
                <?php foreach ($faculties as $faculty): ?>
                <option value="<?= htmlspecialchars($faculty->facultyId); ?>" <?= ($faculty->facultyId === $user->facultyId) ? 'selected' : ''; ?>><?= htmlspecialchars($faculty->facultyName); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="facultyid">Faculty</label>
            <output class="field-error-message" id="facultyid-error" for="facultyid"></output>
        </div>
        
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>">
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>">
        
        <output class="form-status-message" role="status"></output>
        
        <div class="controls-container">
            <button type="button" onclick="history.back();">Back</button>
            <button type="submit" id="edit-profile-submit" disabled>Save Changes</button>
        </div>
        
        <p>Want to change your password? <a href="/change-password">Click here</a>.</p>
    </fieldset>
</form>
