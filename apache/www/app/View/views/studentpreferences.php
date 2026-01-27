<?php 
/**
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\FacultyDto $faculty
 * @var string $userId
*/

$this->extend('main-layout', [
    'title' => 'Unibostu - Student Preferences',
    'userId' => $userId,
    'courses' => $courses
]);
?>

<h2>Student preferences</h2>
    <form action="#" method="post">
        <fieldset class="student-preferences-fieldset">
            <legend>Courses of <?= htmlspecialchars($faculty->facultyName) ?></legend>
            <div>
                <?php foreach ($courses ?? [] as $course): ?>
                    <p>
                        <input type="checkbox" id="course_<?= htmlspecialchars($course->courseId) ?>" name="courses" value="<?= htmlspecialchars($course->courseName) ?>" />
                        <label for="course_<?= htmlspecialchars($course->courseId) ?>"><?= htmlspecialchars($course->courseName) ?></label>
                    </p>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <p><input type="submit" value="Save" /></p>
    </form>
    <p><a href="/users">Go back to profile</a></p>
