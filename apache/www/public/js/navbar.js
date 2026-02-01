const toggleButton = document.getElementById('open-sidebar-button');
const navbar = document.getElementById('navbar');
const overlayPage = document.getElementById('overlay');

// mobile first, navbar inizialmente nascosta
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
    // Se la navbar non ha contenuto scrollabile, lascia passare l'evento al body
    return;
  }
  
  const isAtTop = navbar.scrollTop === 0;
  const isAtBottom = navbar.scrollTop + navbar.clientHeight >= navbar.scrollHeight;
  
  // Previeni lo scroll del body solo se la navbar può ancora scrollare
  if ((event.deltaY < 0 && isAtTop) || (event.deltaY > 0 && isAtBottom)) {
    // Se siamo al limite della navbar, lascia passare l'evento al body
    return;
  }
  
  // Altrimenti, previeni la propagazione
  event.stopPropagation();
}, { passive: true });

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
