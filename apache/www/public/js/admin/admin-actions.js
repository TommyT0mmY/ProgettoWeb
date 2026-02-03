/**
 * Generic admin actions handler for all entities (categories, faculties, courses, tags)
 * Handles: edit, delete, add, view actions
 */

document.addEventListener('DOMContentLoaded', () => {
    // Handle all action buttons
    document.addEventListener('click', async (e) => {
        const button = e.target.closest('[data-action]');
        if (!button) return;
        
        e.preventDefault();
        
        const action = button.dataset.action;
        const entityType = button.dataset.entity;
        const entityId = button.dataset.id;
        const url = button.dataset.url;
        
        switch (action) {
            case 'edit':
            case 'add':
            case 'view':
                // Navigate to the page
                window.location.href = url;
                break;
                
            case 'delete':
                await handleDelete(entityType, entityId, url, button);
                break;
        }
    });
});

/**
 * Handle delete action with confirmation
 */
async function handleDelete(entityType, entityId, apiUrl, button) {
    // Show confirmation dialog
    const entityName = entityType.charAt(0).toUpperCase() + entityType.slice(1);
    const confirmed = confirm(`Are you sure you want to delete this ${entityName}?`);
    
    if (!confirmed) return;
    
    try {
        // Disable button
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Deleting...';
        
        const response = await fetch(apiUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                'csrf-token': window.csrfToken,
                'csrf-key': window.csrfKey
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Remove the card from DOM
            const card = button.closest('.card');
            if (card) {
                card.remove();
            } else {
                window.location.reload();
            }
        } else {
            button.disabled = false;
            button.textContent = originalText;
            alert(data.message || 'Failed to delete.');
        }
    } catch (error) {
        console.error('Delete error:', error);
        button.disabled = false;
        button.textContent = originalText;
        alert('Network error. Please try again.');
    }
}
