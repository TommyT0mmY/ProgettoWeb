import Button from './modules/button.js';

document.addEventListener('DOMContentLoaded', () => {
    const banButton = document.getElementById('ban-user-btn');
    const unbanButton = document.getElementById('unban-user-btn');
    
    if (banButton) {
        const userId = banButton.dataset.userId;
        
        new Button(banButton, {
            confirmMessage: `Are you sure you want to ban user ${userId}? This will prevent them from accessing the platform.`,
            loadingText: 'Banning...',
            onClick: async () => {
                const response = await fetch(`/api/users/${userId}/suspension`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        [window.csrfKey]: window.csrfToken
                    },
                    body: JSON.stringify({ action: 'ban' })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to ban user');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to ban user');
                }
            },
            onError: (error) => {
                console.error('Error banning user:', error);
                alert('Error banning the user. Please try again.');
            }
        }).init();
    }
    
    if (unbanButton) {
        const userId = unbanButton.dataset.userId;
        
        new Button(unbanButton, {
            confirmMessage: `Are you sure you want to unban user ${userId}? They will regain access to the platform.`,
            loadingText: 'Unbanning...',
            onClick: async () => {
                const response = await fetch(`/api/users/${userId}/suspension`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        [window.csrfKey]: window.csrfToken
                    },
                    body: JSON.stringify({ action: 'unban' })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to unban user');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Failed to unban user');
                }
            },
            onError: (error) => {
                console.error('Error unbanning user:', error);
                alert('Error unbanning the user. Please try again.');
            }
        }).init();
    }
});
