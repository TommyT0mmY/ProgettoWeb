import { PostManager } from './PostManager.js';

// Inizializza il PostManager quando il DOM è pronto
document.addEventListener('DOMContentLoaded', () => {
    const userId = window.currentUser || null;
    
    if (userId) {
        new PostManager(userId);
    } else {
        console.warn('Utente non autenticato.');
    }
});
