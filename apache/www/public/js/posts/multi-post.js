import { PostManager } from './PostManager.js';
import { InfiniteScroll } from './infinitescroll.js';

document.addEventListener('DOMContentLoaded', () => {
    const userId = window.currentUser || null;
    
    if (userId) {
        const postManager = new PostManager(userId);
        new InfiniteScroll(postManager);
    } else {
        console.warn('Utente non autenticato.');
    }
});
