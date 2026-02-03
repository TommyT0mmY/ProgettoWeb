const toggleButton = document.getElementById('open-sidebar-button');
const navbar = document.getElementById('navbar');
const overlayPage = document.getElementById('overlay');

// mobile first, navbar initially hidden
navbar.inert = true;

// toggle sidebar
function toggleSidebar() {
  const isOpen = document.body.classList.contains('nav-is-open');
  
  if (isOpen) {
    closeSidebar();
  } else {
    openSidebar();
  }
}

function openSidebar() {
  document.body.classList.add('nav-is-open');
  navbar.inert = false;
  overlayPage.style.display = 'block';
  toggleButton.setAttribute('aria-expanded', 'true');
}

function closeSidebar() {
  document.body.classList.remove('nav-is-open');
  navbar.inert = true;
  overlayPage.style.display = 'none';
  toggleButton.setAttribute('aria-expanded', 'false');
}

toggleButton.addEventListener('click', toggleSidebar);
overlayPage.addEventListener('click', closeSidebar);

// Prevent scroll propagation from navbar to body
navbar.addEventListener('wheel', (event) => {
  const isScrollable = navbar.scrollHeight > navbar.clientHeight;
  
  if (!isScrollable) {
    // If the navbar has no scrollable content, pass the event to the body
    return;
  }
  
  const isAtTop = navbar.scrollTop === 0;
  const isAtBottom = navbar.scrollTop + navbar.clientHeight >= navbar.scrollHeight;
  
  // Prevent body scroll only if the navbar can still scroll
  if ((event.deltaY < 0 && isAtTop) || (event.deltaY > 0 && isAtBottom)) {
    // If we're at the navbar limit, pass the event to the body
    return;
  }
  
  // Otherwise, prevent propagation
  event.stopPropagation();
}, { passive: true });
