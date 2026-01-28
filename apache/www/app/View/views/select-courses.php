<?php 
/** 
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\FacultyDTO[] $faculties
 * @var int|null $userFacultyId
 */
$this->extend('loggedout-layout', [
    'title' => 'Select Courses - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/select-courses.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
        '<link rel="stylesheet" href="/css/select-courses.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);

?>

<form class="fullscreen-form" id="select-courses-form" method="post" novalidate>
    <fieldset>
        <legend>Manage Courses</legend>
        <output class="form-error-message" for="courses" role="alert"></output>

        <!-- Faculty Selector -->
        <div class="field-holder">
            <select id="faculty-selector" aria-describedby="faculty-selector-error">
                <?php foreach ($faculties as $faculty): ?>
                <option value="<?= htmlspecialchars($faculty->facultyId); ?>"<?= $userFacultyId === $faculty->facultyId ? ' selected' : ''; ?>>
                    <?= htmlspecialchars($faculty->facultyName); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <label for="faculty-selector">Faculty</label>
            <output class="field-error-message" id="faculty-selector-error" for="faculty-selector"></output>
        </div>

        <!-- Courses Container -->
        <div id="courses-container" aria-label="Courses list">
        </div>

        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>" />
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>" />
        <div class="controls-container">
            <button type="button" onclick="history.back()">Back</button>
            <button type="submit" id="select-courses-submit" disabled>Save</button>
        </div>
    </fieldset>
</form>

