import { deletePost, likePost, dislikePost } from './PostApi.js';

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
        
        // Aggiungi event listener
        deleteBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            if (!confirm('Sei sicuro di voler eliminare questo post?')) {
                return;
            }
            
            try {
                const result = await deletePost(postId);
                
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    alert('Errore durante l\'eliminazione del post: ' + result.message);
                }
            } catch (error) {
                console.error('Error deleting post:', error);
                alert('Errore durante l\'eliminazione del post');
            }
        });
    }
    
    setupReactionButtons(postElement, postId) {
        const likeBtn = postElement.querySelector('.btn-like');
        const dislikeBtn = postElement.querySelector('.btn-dislike');
        const likeData = postElement.querySelector('.reaction-like data');
        const dislikeData = postElement.querySelector('.reaction-dislike data');
        
        if (!likeBtn || !dislikeBtn) {
            return;
        }
        
        likeBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const result = await likePost(postId);
                this.updateReactionUI(postElement, result);
            } catch (error) {
                console.error('Error liking post:', error);
                alert('Errore durante il like del post');
            }
        });
        
        dislikeBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const result = await dislikePost(postId);
                this.updateReactionUI(postElement, result);
            } catch (error) {
                console.error('Error disliking post:', error);
                alert('Errore durante il dislike del post');
            }
        });
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
