<?php
/**
 * @var array<\Unibostu\Model\DTO\CourseDTO> $courses
 * @var string $userId
 */

// Raggruppa i corsi per facoltà
$coursesByFaculty = [];
foreach ($courses as $course) {
    $facultyName = $course->facultyName ?? 'Other';
    if (!isset($coursesByFaculty[$facultyName])) {
        $coursesByFaculty[$facultyName] = [];
    }
    $coursesByFaculty[$facultyName][] = $course;
}
?>
<nav id="navbar" aria-label="Main navigation">
    <button id="logout-button" class="btn btn-secondary">Logout</button>
    <section aria-labelledby="courses-heading">
        <h2 id="courses-heading">Your courses</h2>
        <? if (empty($coursesByFaculty)): ?>
        <p>You are not enrolled in any courses.</p>
        <? else: ?>
        <ul class="nav-faculties">
            <?php foreach ($coursesByFaculty as $facultyName => $facultyCourses): ?>
            <li>
                <h3><?= h($facultyName) ?></h3>
                <ul class="nav-courses">
                    <?php foreach($facultyCourses as $course): ?>
                    <li>
                        <a class="btn btn-primary" href="/courses/<?= h($course->courseId); ?>"><?= h($course->courseName); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>
        </ul>
        <? endif; ?>
    </section>
</nav>
