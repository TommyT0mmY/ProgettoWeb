import { deletePost, likePost, dislikePost } from './PostApi.js';
import Button from '../modules/button.js';

export class PostManager {
    /** @type {string} */
    #currentUser;
    
    /**
     * @param {string} currentUser - ID dell'utente corrente
     */
    constructor(currentUser) {
        this.#currentUser = currentUser;
        this.init();
    }
    
    init() {
        // Trova tutti i post nella pagina
        const posts = document.querySelectorAll('.Post');
        
        posts.forEach(post => {
            this.setupPostListeners(post);
        });
    }
    
    setupPostListeners(postElement) {
        const postId = parseInt(postElement.dataset.postId);
        const authorId = postElement.dataset.authorId;
        
        // Crea e setup del pulsante delete se l'utente è l'autore
        if (this.#currentUser && this.#currentUser === authorId) {
            this.createAndSetupDeleteButton(postElement, postId);
        }
        
        // Setup like/dislike buttons
        this.setupReactionButtons(postElement, postId);
    }
    
    createAndSetupDeleteButton(postElement, postId) {
        const reviewList = postElement.querySelector('.review');
        if (!reviewList) return;
        
        // Crea l'elemento li per il pulsante delete
        const deleteItem = document.createElement('li');
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn-delete-post';
        deleteBtn.textContent = 'Delete';
        deleteBtn.type = 'button';
        
        deleteItem.appendChild(deleteBtn);
        
        // Inserisci il pulsante dopo l'attachment (se esiste) o all'inizio
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
                
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    throw new Error(result.message || 'Error deleting the post');
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
        
        // Use Button utility for like functionality
        new Button(likeBtn, {
            stopPropagation: true,
            onClick: async () => {
                const result = await likePost(postId);
                this.updateReactionUI(postElement, result);
            },
            onError: (error) => {
                console.error('Error liking post:', error);
                alert('Error liking the post');
            }
        }).init();
        
        // Use Button utility for dislike functionality
        new Button(dislikeBtn, {
            stopPropagation: true,
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
     * Aggiorna la UI dei like/dislike in base alla risposta del server
     * @param {HTMLElement} postElement 
     * @param {Object} result - {likes: number, dislikes: number, userReaction: 'like'|'dislike'|null}
     */
    updateReactionUI(postElement, result) {
        const likeBtn = postElement.querySelector('.btn-like');
        const dislikeBtn = postElement.querySelector('.btn-dislike');
        const likeData = postElement.querySelector('.reaction-like data');
        const dislikeData = postElement.querySelector('.reaction-dislike data');
        
        // Aggiorna i contatori
        if (likeData) {
            likeData.textContent = result.likes;
            likeData.setAttribute('value', result.likes);
        }
        
        if (dislikeData) {
            dislikeData.textContent = result.dislikes;
            dislikeData.setAttribute('value', result.dislikes);
        }
        
        // Rimuovi le classi active da entrambi i pulsanti
        likeBtn.classList.remove('active');
        dislikeBtn.classList.remove('active');
        
        // Il server ritorna userReaction come 'like', 'dislike' o null
        // Aggiungi la classe active al pulsante corretto
        if (result.userReaction === 'like') {
            likeBtn.classList.add('active');
        } else if (result.userReaction === 'dislike') {
            dislikeBtn.classList.add('active');
        }
    }
}
