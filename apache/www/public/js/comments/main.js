import { CommentManager } from './CommentManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const commentsSection = document.getElementById('comments-section');
    
    if (!commentsSection) {
        console.warn('Comments section not found');
        return;
    }
    
    const postId = commentsSection.dataset.postId;
    
    if (!postId) {
        console.error('Post ID not found');
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
        console.error('Error initializing comments:', error);
        commentsSection.innerHTML = `
            <div class="comments-error">
                <p>Error loading comments</p>
            </div>
        `;
    }
});