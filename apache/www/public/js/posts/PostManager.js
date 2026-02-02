import { deletePost, likePost, dislikePost } from './PostApi.js';
import Button from '../modules/button.js';

export class PostManager {
    /** @type {string} */
    #currentUser;
    /** @type {boolean} */
    #isAdmin;
    
    /**
     * @param {string} currentUser - Current user ID
     * @param {boolean} isAdmin - Whether the current user is an admin
     */
    constructor(currentUser, isAdmin = false) {
        this.#currentUser = currentUser;
        this.#isAdmin = isAdmin;
        this.init();
    }
    
    init() {
        // Find all posts on the page
        const posts = document.querySelectorAll('.post');
        
        posts.forEach(post => {
            this.setupPostListeners(post);
        });
    }
    
    setupPostListeners(postElement) {
        const postId = parseInt(postElement.dataset.postId);
        const authorId = postElement.dataset.authorId;
        
        // Create and setup delete button if user is the author or admin
        if (this.#currentUser && (this.#currentUser === authorId || this.#isAdmin)) {
            this.createAndSetupDeleteButton(postElement, postId);
        }
        
        // Setup like/dislike buttons
        this.setupReactionButtons(postElement, postId);
    }
    
    createAndSetupDeleteButton(postElement, postId) {
        const reviewList = postElement.querySelector('.review');
        if (!reviewList) return;
        
        // Create li element for delete button
        const deleteItem = document.createElement('li');
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn-delete-post';
        deleteBtn.textContent = 'Delete';
        deleteBtn.type = 'button';
        
        deleteItem.appendChild(deleteBtn);
        
        // Insert button after attachment (if exists) or at the beginning
        const attachmentItem = reviewList.querySelector('li:first-child');
        if (attachmentItem && attachmentItem.querySelector('a[download]')) {
            attachmentItem.insertAdjacentElement('afterend', deleteItem);
        } else {
            reviewList.insertBefore(deleteItem, reviewList.firstChild);
        }
        
        // Use Button utility for delete functionality
        new Button(deleteBtn, {
            confirmMessage: 'Are you sure you want to delete this post?',
            loadingText: 'Deleting...',
            stopPropagation: true,
            onClick: async () => {
                const result = await deletePost(postId);
                
                if (result.success && result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    throw new Error('Error deleting the post');
                }
            },
            onError: (error) => {
                console.error('Error deleting post:', error);
                alert('Error deleting the post');
            }
        }).init();
    }
    
    setupReactionButtons(postElement, postId) {
        const likeBtn = postElement.querySelector('.btn-like');
        const dislikeBtn = postElement.querySelector('.btn-dislike');
        
        if (!likeBtn || !dislikeBtn) {
            return;
        }
        
        // If admin, disable the buttons visually and functionally
        if (this.#isAdmin) {
            // Aggiungi la classe 'disabled' agli elementi <li> padre dei bottoni
            const likeLi = likeBtn.closest('li.reaction');
            const dislikeLi = dislikeBtn.closest('li.reaction');
            
            if (likeLi) {
                likeLi.classList.add('disabled');
            }
            if (dislikeLi) {
                dislikeLi.classList.add('disabled');
            }
            
            likeBtn.disabled = true;
            dislikeBtn.disabled = true;
            
            return; // Don't attach click handlers for admin
        }
        
        // Use Button utility for like functionality on the button element
        new Button(likeBtn, {
            stopPropagation: true,
            loadingText: '',
            onClick: async () => {
                const result = await likePost(postId);
                this.updateReactionUI(postElement, result);
            },
            onError: (error) => {
                console.error('Error liking post:', error);
                alert('Error liking the post');
            }
        }).init();
        
        // Use Button utility for dislike functionality on the button element
        new Button(dislikeBtn, {
            stopPropagation: true,
            loadingText: '',
            onClick: async () => {
                const result = await dislikePost(postId);
                this.updateReactionUI(postElement, result);
            },
            onError: (error) => {
                console.error('Error disliking post:', error);
                alert('Error disliking the post');
            }
        }).init();
    }
    
    /**
     * Updates the like/dislike UI based on server response
     * @param {HTMLElement} postElement 
     * @param {Object} result - {likes: number, dislikes: number, userReaction: 'like'|'dislike'|null}
     */
    updateReactionUI(postElement, result) {
        const likeBtn = postElement.querySelector('.btn-like');
        const dislikeBtn = postElement.querySelector('.btn-dislike');
        const likeData = likeBtn?.querySelector('data[data-field="likes"]');
        const dislikeData = dislikeBtn?.querySelector('data[data-field="dislikes"]');
        
        // Update counters
        if (likeData) {
            likeData.textContent = result.likes;
            likeData.setAttribute('value', result.likes);
        }
        
        if (dislikeData) {
            dislikeData.textContent = result.dislikes;
            dislikeData.setAttribute('value', result.dislikes);
        }
        
        // Remove active class from both buttons
        likeBtn.classList.remove('active');
        dislikeBtn.classList.remove('active');
        
        // Server returns userReaction as 'like', 'dislike' or null
        // Add active class to the correct button
        if (result.userReaction === 'like') {
            likeBtn.classList.add('active');
        } else if (result.userReaction === 'dislike') {
            dislikeBtn.classList.add('active');
        }
    }
}
