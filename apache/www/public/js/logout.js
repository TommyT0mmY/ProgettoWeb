// Logout functionality - shared between admin and user layouts
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
