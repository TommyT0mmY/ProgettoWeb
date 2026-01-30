<?php
/**
 * @var array<\Unibostu\Model\DTO\CourseDTO> $courses
 */
?>
<nav id="navbar" aria-label="Main navigation">
  <div class="sidebar-content">
    <ul class="nav-main">
      <li><a href="/">Home</a></li>
      <li><a href="/register">Register</a></li>
      <li><a href="/login">Login</a></li>
      <li><a href="#" id="logout-link">Logout</a></li>
      <li><a href="/studentpreferences">Preferences</a></li>
    </ul>
    
    <section>
      <h2 class="h2-navbar">Your courses</h2>
      <ul class="nav-courses">
        <?php foreach($courses as $course): ?>
          <li>
            <a href="/courses/<?= htmlspecialchars($course->courseId); ?>"><?= htmlspecialchars($course->courseName); ?></a>
          </li>
        <?php endforeach; ?>
          <li>
            <a href="/select-courses">Browse all courses</a>
          </li>
      </ul>
    </section>
  </div>
</nav>
