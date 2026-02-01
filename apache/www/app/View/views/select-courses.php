<?php 
/** 
 * @var \Unibostu\Core\RenderingEngine $this 
 * @var array<Unibostu\Model\DTO\FacultyDTO> $faculties
 * @var int $userId
 * @var int $userFacultyId The faculty ID of the user, preselect this in the dropdown
 */
$this->extend('loggedout-layout', [
    'title' => 'Select Courses - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/select-courses.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
        '<link rel="stylesheet" href="/css/select-courses.css" />'
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>

<form class="fullscreen-form" id="select-courses-form" method="post" novalidate>
    <fieldset>
        <legend>Manage Courses</legend>
        <output class="form-error-message" role="alert"></output>

        <div class="field-holder">
            <select name="facultyid" id="facultyid" aria-describedby="facultyid-error" required>
                <?php foreach ($faculties as $faculty): ?>

                <option value="<?=h($faculty->facultyId);?>" <?=($faculty->facultyId === $userFacultyId) ? 'selected ' : '';?>><?=h($faculty->facultyName);?></options>

                <?php endforeach; ?>
            </select>
            <label for="facultyid">Faculty ID</label>
            <output class="field-error-message" id="facultyid-error" for="facultyid"></output>
        </div>
        <div class="courses-container">
            <!-- Courses will be dynamically loaded here based on faculty selection -->
        </div>
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>" />
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>" />
        <output class="form-status-message" role="status"></output>
        <div class="controls-container">
            <button type="button" id="select-courses-back" onclick="history.back();">Back</button>
            <button type="submit" id="select-courses-submit" disabled>Save Preferences</button>
        </div>
    </fieldset>
</form>


<template id="course-template">
    <div class="field-holder course-item">
        <input type="checkbox" name="courses[]" id="" value="" />
        <label for=""></label>
    </div>
</template>

