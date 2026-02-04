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

