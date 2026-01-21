import { CommentManager } from './CommentManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const commentsSection = document.getElementById('comments-section');
    
    if (!commentsSection) {
        console.warn('Sezione commenti non trovata');
        return;
    }
    
    const postId = commentsSection.dataset.postId;
    
    if (!postId) {
        console.error('ID post non trovato');
        return;
    }
    const currentUser = window.currentUser || null;

    try {
        const commentManager = new CommentManager(
            postId, 
            commentsSection, 
            currentUser
        );
                window.commentManager = commentManager;
    } catch (error) {
        console.error('Errore inizializzazione commenti:', error);
        commentsSection.innerHTML = `
            <div class="comments-error">
                <p>Errore nel caricamento dei commenti</p>
            </div>
        `;
    }
});