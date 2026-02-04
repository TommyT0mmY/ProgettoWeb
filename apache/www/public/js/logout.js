document.getElementById("logout-button")?.addEventListener("click", async (event) => {
    const button = event.currentTarget;
    event.preventDefault();
    button.disabled = true;
    button.textContent = "Logging out...";
    try {
        const response = await fetch('/api/auth/logout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                'csrf-key': window.csrfKey,
                'csrf-token': window.csrfToken
            }),
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

