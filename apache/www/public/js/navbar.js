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

const logoutLink = document.getElementById('logout-link');

if (logoutLink) {
  logoutLink.addEventListener('click', async (event) => {
    event.preventDefault();
    try {
      const response = await fetch('/api/auth/logout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          'csrf-key': window.csrfKey,
          'csrf-token': window.csrfToken
        })
      });
      const data = await response.json();
      if (data?.redirect) {
        window.location.href = data.redirect;
        return;
      }
      if (response.ok) {
        window.location.href = '/login';
      }
    } catch (error) {
      window.location.href = '/login';
    }
  });
}
