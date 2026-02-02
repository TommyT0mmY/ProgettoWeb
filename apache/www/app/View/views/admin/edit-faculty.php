<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\FacultyDTO $faculty
 * @var string $adminId
 */

$this->extend('loggedout-layout', [
    'title' => 'Edit Faculty - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/edit-faculty.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form class="fullscreen-form" id="edit-faculty-form" method="post" novalidate>
    <fieldset>
        <legend>Edit Faculty</legend>
        <output class="form-error-message" for="facultyname" role="alert"></output>
        
        <div class="field-holder">
            <input type="text" name="facultyid" id="facultyid" value="<?= h($faculty->facultyId); ?>" disabled />
            <label for="facultyid">Faculty ID</label>
        </div>
        
        <div class="field-holder">
            <input type="text" name="facultyname" id="facultyname" aria-describedby="facultyname-error" value="<?= h($faculty->facultyName ?? ''); ?>" required />
            <label for="facultyname">Faculty Name</label>
            <output class="field-error-message" id="facultyname-error" for="facultyname"></output>
        </div>

        <div class="courses-container">
            <!-- Courses will be dynamically loaded here based on faculty selection -->
        </div>
        
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>" />
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>" />
        <input type="hidden" name="facultyid" value="<?= h($faculty->facultyId); ?>" />

        <output class="form-status-message" role="status"></output>
        
        <div class="controls-container">            
            <button type="button" onclick="window.location.href='/faculties'">Back</button>
            <button type="submit" id="edit-faculties-submit" disabled>Save Changes</button>
        </div>
        
    </fieldset>
</form>
