<nav id="navbar" aria-label="Main navigation">
  <div>
    <ul class="nav-main">
      <li>
        <button id="close-sidebar-button" aria-label="Close navigation menu">
          <img class="menu-container" src="/images/icons/close-sidebar.svg" alt="" />
        </button>
      </li>
      <li><a href="/">Home</a></li>
      <li><a href="/register.php">Register</a></li>
      <li><a href="/login.php">Login</a></li>
      <li><a href="/logout.php">Logout</a></li>
      <li><a href="/studentpreferences.php">Preferences</a></li>
    </ul>
    
    <section>
      <h2 class="h2-navbar">Your courses</h2>
      <ul class="nav-courses">
        <?php foreach($courses as $course): ?>
          <li>
            <a href="/courses/<?= htmlspecialchars($course->courseId); ?>"><?= htmlspecialchars($course->courseName); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>
</nav>
