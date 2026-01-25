import { fetchComments, postComment, deleteComment } from './CommentsApi.js';
import Button from '../modules/button.js';

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
                    node.children.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
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
    
    renderComment(comment, depth = 0, parentAuthor = null) {
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
            deleteBtn.textContent = 'Delete';
            
            commentActions.appendChild(deleteBtn);
            
            // Use Button utility for delete functionality
            new Button(deleteBtn, {
                confirmMessage: 'Are you sure you want to delete this comment?',
                loadingText: 'Deleting...',
                errorMessage: 'Error deleting the comment',
                stopPropagation: true,
                onClick: async () => {
                    const success = await deleteComment(this.#postId, comment.commentId);
                    if (success) {
                        this.markCommentAsDeleted(success, comment.commentId);
                        this.render();
                    } else {
                        throw new Error('Deletion failed');
                    }
                }
            }).init();
        }

        const textElement = commentEl.querySelector('.comment-text');
        
        // If it's a reply, add @mention to parent author
        if (parentAuthor) {
            const mention = document.createElement('span');
            mention.className = 'comment-mention';
            mention.textContent = `@${parentAuthor.userId} `;
            textElement.innerHTML = '';
            textElement.appendChild(mention);
            textElement.appendChild(document.createTextNode(comment.text));
        } else {
            textElement.textContent = comment.text;
        }
        
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
                // Passing the parent author to add @mention
                repliesContainer.appendChild(this.renderComment(child, depth + 1, comment.author));
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
            new Button(cancelBtn, {
                preventDefault: true,
                onClick: async () => {
                    form.remove();
                }
            }).init();
        }
        
        // Use Button utility for submit functionality
        new Button(submitBtn, {
            loadingText: parentCommentId !== null ? 'Sending...' : 'Sending...',
            errorMessage: 'Error sending comment. Please try again.',
            onClick: async () => {
                const text = textarea.value.trim();
                if (!text) {
                    throw new Error('Comment cannot be empty');
                }
                
                const newComment = await postComment({
                    postid: this.#postId,
                    parentCommentId: parentCommentId,
                    text: text
                });

                this.addCommentToTree(newComment);
                this.render();

                textarea.value = '';
                // Remove the form if it was a reply
                if (parentCommentId !== null) {
                    form.remove();
                }
            }
        }).init();
        
        // Alternative: use form submit event instead
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            submitBtn.click();
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
        
        // Remove only reply forms (those inside comments or comment-list), never the main form
        document.querySelectorAll('.comment-form').forEach(form => {
            const isInsideComment = form.closest('.comment');
            const isInsideCommentList = form.closest('.comments-list');
            
            // Remove only if it's inside a comment or comment-list AND not in the current comment
            if ((isInsideComment || isInsideCommentList) && !form.closest(`[data-comment-id="${commentId}"]`)) {
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
                parent.children.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
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