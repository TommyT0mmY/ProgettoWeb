<?php
/**
 * @var array<\Unibostu\Model\DTO\CourseDTO> $courses
 * @var string $userId
 */
?>
<nav id="navbar" aria-label="Main navigation">
  <div class="sidebar-content">
    <ul class="nav-main">
      <li><a href="#" id="logout-link">Logout</a></li>
    </ul>
    
    <section>
      <h2 class="h2-navbar">Your courses</h2>
      <ul class="nav-courses">
        <?php foreach($courses as $course): ?>
          <li>
            <a href="/courses/<?= h($course->courseId); ?>"><?= h($course->courseName); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>
</nav>
