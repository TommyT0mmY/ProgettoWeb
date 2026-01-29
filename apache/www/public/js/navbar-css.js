const toggleButton = document.getElementById('open-sidebar-button');
const navbar = document.getElementById('navbar');
const overlayPage = document.getElementById('overlay');

// mobile first, navbar inizialmente nascosta
navbar.inert = true;

// toggle sidebar
function toggleSidebar() {
  const isOpen = navbar.classList.contains('show');
  
  if (isOpen) {
    closeSidebar();
  } else {
    openSidebar();
  }
}

function openSidebar() {
  navbar.classList.add('show');
  navbar.inert = false;
  overlayPage.style.display = 'block';
  toggleButton.setAttribute('aria-expanded', 'true');
}

function closeSidebar() {
  navbar.classList.remove('show');
  navbar.inert = true;
  overlayPage.style.display = 'none';
  toggleButton.setAttribute('aria-expanded', 'false');
}

toggleButton.addEventListener('click', toggleSidebar);
overlayPage.addEventListener('click', closeSidebar);
