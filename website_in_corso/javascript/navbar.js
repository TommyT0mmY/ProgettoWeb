const openButton = document.getElementById('open-sidebar-button');
const navbar = document.getElementById('navbar');
const closeButton = document.getElementById('close-sidebar-button');
const overlayPage = document.getElementById('overlay');
//mobile first, ma l' icona menu permane quindi ok
navbar.inert = true;

//open sideBar
function openSidebar() {
  navbar.classList.add('show');
  navbar.inert = false;
  overlayPage.style.display = 'block';
}
openButton.addEventListener('click', openSidebar);

//close sideBar
function closeSidebar(){
  navbar.classList.remove('show')
  navbar.inert = true;
  overlayPage.style.display = "none";
}
closeButton.addEventListener("click", closeSidebar);
overlayPage.addEventListener("click", closeSidebar);
