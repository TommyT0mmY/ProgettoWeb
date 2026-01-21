import { fetchComments, postComment, deleteComment } from './CommentsApi.js';

export class CommentManager {
    /** @type {int} */
    #postId;
    /** @type {HTMLElement} */
    #container;
    /** @type {int} */
    #currentUser;
    /** @type {Array} */
    #comments;

    /**
     * @param {int} postId 
     * @param {HTMLElement} container 
     * @param {int} currentUser
     */
    constructor(postId, container, currentUser) {
        this.#postId = postId;
        this.#container = container;
        this.#currentUser = currentUser; 
        this.#comments = [];
        
        this.commentTemplate = document.getElementById('comment-template');
        this.commentFormTemplate = document.getElementById('comment-form-template');
        
        if (!this.commentTemplate || !this.commentFormTemplate) {
            throw new Error('Templates non trovati');
        }
        
        this.init();
    }
    
    async init() {
        try {
            await this.loadComments();
        } catch (error) {
            this.showError('Impossibile caricare i commenti');
        }
    }
    
    async loadComments() {        
        try {
            const flatComments = await fetchComments(this.#postId);
            this.#comments = this.buildTree(flatComments);
            this.render();
        } catch (error) {
            this.showError('Errore nel caricamento dei commenti');
            throw error;
        }
    }
    
    /**
     * This function builds a tree structure from a flat array of commentDTOs.
     * @param {CommentDTO[]} flatArray 
     */
    buildTree(flatArray) {
        const map = new Map();
        const tree = [];
        
        flatArray.forEach(comment => {
            map.set(comment.commentId, {
                ...comment,
                children: []
            });
        });
        
        flatArray.forEach(comment => {
            const node = map.get(comment.commentId);
            
            if (comment.parentCommentId) {
                const parent = map.get(comment.parentCommentId);
                if (parent) {
                    parent.children.push(node);
                } else {
                    tree.push(node);
                }
            } else {
                tree.push(node);
            }
        });
        
        // Order by date descending
        tree.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
        
        const sortChildren = (nodes) => {
            nodes.forEach(node => {
                if (node.children.length > 0) {
                    node.children.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
                    sortChildren(node.children);
                }
            });
        };
        
        sortChildren(tree);
        
        return tree;
    }
    
    render() {
        this.#container.innerHTML = '';
        
        const title = document.createElement('h2');
        title.className = 'comments-title';
        title.textContent = `Comments`;
        this.#container.appendChild(title);

        this.#container.appendChild(this.createCommentForm());

        if (this.#comments.length === 0) {
            this.showEmptyState();
            return;
        }

        const commentsList = document.createElement('div');
        commentsList.className = 'comments-list';
        
        this.#comments.forEach(comment => {
            commentsList.appendChild(this.renderComment(comment));
        });

        this.#container.appendChild(commentsList);
    }
    
    renderComment(comment, depth = 0) {
        const clone = this.commentTemplate.content.cloneNode(true);
        const commentEl = clone.querySelector('.comment');
        
        commentEl.dataset.commentId = comment.commentId;
        commentEl.dataset.depth = depth;
        
        if (comment.deleted) {
            commentEl.classList.add('comment-deleted');
        }
        
        commentEl.querySelector('.comment-author-name').textContent = `${comment.author.firstName} ${comment.author.lastName}`;
        
        const dateElement = commentEl.querySelector('.comment-date');
        dateElement.textContent = comment.createdAt;
        dateElement.setAttribute('datetime', comment.createdAt);
        
        const isAuthor = this.#currentUser && 
                this.#currentUser === comment.author.userId;
        
        const commentActions = commentEl.querySelector('.comment-actions');
        
        if (isAuthor && !comment.deleted) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn-delete';
            deleteBtn.textContent = 'Elimina';
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.confirmDelete(comment.commentId);
            });
            
            commentActions.appendChild(deleteBtn);
        }

        const textElement = commentEl.querySelector('.comment-text');
        textElement.textContent = comment.text;        
        // Add event listener if not deleted
        if (!comment.deleted) {
            const replyBtn = commentEl.querySelector('.btn-reply');
            replyBtn.addEventListener('click', () => this.showReplyForm(comment.commentId));
        } else {
            // Disable buttons for deleted comments
            const replyBtn = commentEl.querySelector('.btn-reply');
            replyBtn.disabled = true;
        }
        
        if (comment.children && comment.children.length > 0) {
            const repliesContainer = commentEl.querySelector('.comment-replies');
            comment.children.forEach(child => {
                repliesContainer.appendChild(this.renderComment(child, depth + 1));
            });
        }
        
        return commentEl;
    }
    
    createCommentForm(parentCommentId = null) {
        const clone = this.commentFormTemplate.content.cloneNode(true);
        const form = clone.querySelector('.comment-form');
        
        const textarea = form.querySelector('.comment-input');
        const submitBtn = form.querySelector('.btn-submit');
        const cancelBtn = form.querySelector('.btn-cancel');
        
        if (parentCommentId === null) {
            cancelBtn.style.display = 'none';
        } else {
            cancelBtn.addEventListener('click', () => {
                form.remove();
           });
        }
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const text = textarea.value.trim();
            if (!text) {
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
            
            try {
                const newComment = await postComment({
                    postid: this.#postId,
                    parentCommentId: parentCommentId,
                    text: text
                });

                this.addCommentToTree(newComment);
                this.render();

                textarea.value = '';
                // Se è una risposta, rimuovi il form
                if (parentCommentId !== null) {
                    form.remove();
                }
                
            } catch (error) {
                alert('Errore nell\'invio del commento. Riprova.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = parentCommentId !== null ? 'Reply' : 'Comment';
            }
        });
        
        return form;
    }
        
    showReplyForm(commentId) {
        // If a form is already open for this comment remove it 
        const commentEl = document.querySelector(`[data-comment-id="${commentId}"]`);
        const existingForm = commentEl.querySelector('.comment-form');
        
        if (existingForm) {
            existingForm.remove();
            return;
        }
        
        document.querySelectorAll('.comment-form').forEach(form => {
            if (!form.closest(`[data-comment-id="${commentId}"]`)) {
                form.remove();
            }
        });
        
        const replyForm = this.createCommentForm(commentId);
        const repliesContainer = commentEl.querySelector('.comment-replies');
        repliesContainer.insertBefore(replyForm, repliesContainer.firstChild);
    }
        
    addCommentToTree(newComment) {
        const newNode = {
            ...newComment,
            children: []
        };
        
        if (newComment.parentCommentId !== null) {
            const parent = this.findCommentById(this.#comments, newComment.parentCommentId);
            if (parent) {
                parent.children.push(newNode);
                parent.children.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
            }
        } else {
            this.#comments.unshift(newNode);
        }
    }
    
    findCommentById(tree, id) {
        for (const comment of tree) {
            if (comment.commentId === id) return comment;
            if (comment.children.length > 0) {
                const found = this.findCommentById(comment.children, id);
                if (found) return found;
            }
        }
        return null;
    }
    
    async confirmDelete(commentId) {
        if (!confirm('Sei sicuro di voler eliminare questo commento?')) {
            return;
        }
        
        try {
            const success = await deleteComment(this.#postId, commentId);
            
            if (success) {

                this.markCommentAsDeleted(success, commentId);
                this.render();
            } else {
                throw new Error('Eliminazione fallita');
            }
        } catch (error) {
            console.error('Errore nell\'eliminazione:', error);
        }
    }

    markCommentAsDeleted(success, commentId) {
        const comment = this.findCommentById(this.#comments, commentId);
        if (comment) {
            comment.text = success.text;
            comment.deleted = true;
        }
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'ora';
        if (diffMins < 60) return `${diffMins} min fa`;
        if (diffHours < 24) return `${diffHours} ore fa`;
        if (diffDays < 7) return `${diffDays} giorni fa`;
        
        return date.toLocaleDateString('it-IT', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }
    
    showLoading() {
        this.#container.innerHTML = `
            <div class="comments-loading">
                <p>Caricamento commenti...</p>
            </div>
        `;
    }
    
    showError(message) {
        this.#container.innerHTML = `
            <div class="comments-error">
                <p>${message}</p>
                <button onclick="window.location.reload()">Riprova</button>
            </div>
        `;
    }
    
    showEmptyState() {
        const emptyState = document.createElement('div');
        emptyState.className = 'comments-empty';
        emptyState.innerHTML = `
            <p>Nessun commento ancora. Sii il primo a commentare!</p>
        `;
        this.#container.appendChild(emptyState);
    }
}