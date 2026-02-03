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
  <div class="sidebar-content">
    <ul class="nav-main">
      <li><a href="#" id="logout-link">Logout</a></li>
    </ul>
    
    <h2>Your courses</h2>
    
    <?php foreach ($coursesByFaculty as $facultyName => $facultyCourses): ?>
    <section>
      <h3><?= h($facultyName) ?></h3>
      <ul class="nav-courses">
        <?php foreach($facultyCourses as $course): ?>
          <li>
            <a href="/courses/<?= h($course->courseId); ?>"><?= h($course->courseName); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endforeach; ?>
  </div>
</nav>
