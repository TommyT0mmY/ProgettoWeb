<?php 
/**
 * @var \Unibostu\Dto\CourseDto[] $courses
 * @var \Unibostu\Dto\FacultyDto $faculty
*/

$this->extend('main-layout', [
    'title' => 'Unibostu - Student Preferences',
    'courses' => $courses,
    'additionalHeadCode' => [
        '<script type="module" src="/js/navbar-css.js"></script>',
        '<link rel="stylesheet" href="/css/style2.css">'
        ],
    ]);
?>

<h2>Student preferences</h2>
    <form action="#" method="post">           
        <fieldset class="student-preferences-fieldset">
            <legend>Courses of <?= htmlspecialchars($faculty->facultyName) ?></legend>
            <div>
                <?php foreach ($courses ?? [] as $course): ?>
                    <input type="checkbox" id="course_<?= htmlspecialchars($course->courseId) ?>" name="courses" value="<?= htmlspecialchars($course->courseName) ?>">
                    <label for="course_<?= htmlspecialchars($course->courseId) ?>"><?= htmlspecialchars($course->courseName) ?></label>
                <?php endforeach; ?>               
            </div>           
        </fieldset>
        <input type="submit" value="Save"/>      
    </form>
    <a href="/user-profile"> go back to profile ⟵</a>