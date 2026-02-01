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
            <legend>Courses of <?= h($faculty->facultyName) ?></legend>
            <div>
                <?php foreach ($courses ?? [] as $course): ?>
                    <p>
                        <input type="checkbox" id="course_<?= h($course->courseId) ?>" name="courses" value="<?= h($course->courseName) ?>" />
                        <label for="course_<?= h($course->courseId) ?>"><?= h($course->courseName) ?></label>
                    </p>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <p><input type="submit" value="Save" /></p>
    </form>
    <p><a href="/users/<?= h($userId) ?>">Go back to profile</a></p>
