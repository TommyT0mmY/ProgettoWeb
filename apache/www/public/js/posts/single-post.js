import { PostManager } from './PostManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const userId = window.currentUser || null;
    const isAdmin = window.isAdmin || false;
    
    if (userId) {
        new PostManager(userId, isAdmin);
    } else {
        console.warn('Utente non autenticato.');
    }
});
