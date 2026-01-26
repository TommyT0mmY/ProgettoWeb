import { PostManager } from './PostManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const userId = window.currentUser || null;
    
    if (userId) {
        new PostManager(userId);
    } else {
        console.warn('Utente non autenticato.');
    }
});
