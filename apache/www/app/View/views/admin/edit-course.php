<?php 
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var \Unibostu\Model\DTO\CourseDTO $course
 * @var \Unibostu\Model\DTO\FacultyDTO $faculty
 * @var string $adminId
 */

$this->extend('loggedout-layout', [
    'title' => 'Edit Course - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/edit-course.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
['csrfKey' => $csrfKey, 'csrfToken' => $csrfToken] = $this->generateCsrfPair(true);
?>
<form class="fullscreen-form" id="edit-course-form" method="post" novalidate>
    <fieldset>
        <legend>Edit Course</legend>
        <output class="form-error-message" for="coursename" role="alert"></output>
        
        <div class="field-holder">
            <input type="text" name="courseid" id="courseid" value="<?= h($course->courseId); ?>" disabled />
            <label for="courseid">Course ID</label>
        </div>
        
        <div class="field-holder">
            <input type="text" name="coursename" id="coursename" aria-describedby="coursename-error" value="<?= h($course->courseName ?? ''); ?>" required />
            <label for="coursename">Course Name</label>
            <output class="field-error-message" id="coursename-error" for="coursename"></output>
        </div>

        <div class="field-holder">
            <input type="text" name="faculty" id="faculty" value="<?= h($faculty->facultyName ?? ''); ?>" disabled />
            <label for="faculty">Faculty</label>
        </div>
        
        <input type="hidden" name="csrf-token" id="csrf-token" value="<?= $csrfToken; ?>" />
        <input type="hidden" name="csrf-key" id="csrf-key" value="<?= $csrfKey; ?>" />
        <input type="hidden" name="courseid" value="<?= h($course->courseId); ?>" />
        <input type="hidden" name="facultyid" value="<?= h($faculty->facultyId); ?>" />

        <output class="form-status-message" role="status"></output>
        
        <div class="controls-container">            
            <button type="button" onclick="window.location.href='/faculties/<?= h($faculty->facultyId) ?>/courses'">Back</button>
            <button type="submit" id="edit-course-submit" disabled>Save Changes</button>
        </div>
        
    </fieldset>
</form>
