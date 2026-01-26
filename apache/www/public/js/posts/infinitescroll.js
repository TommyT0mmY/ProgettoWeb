import { fetchPosts } from './PostApi.js';

export class InfiniteScroll {
    constructor(postManager) {
        this.isLoading = false;
        this.hasMorePosts = true;
        this.postContainer = document.querySelector('.post_container');
        this.lastPostId = this.getLastPostId();
        this.filters = this.getCurrentFilters();
        this.postManager = postManager;
        
        this.init();
    }

    init() {
        window.addEventListener('scroll', () => this.handleScroll());
    }

    getCurrentFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            categoryId: urlParams.get('categoryId') || '',
            sortOrder: urlParams.get('sortOrder') || 'desc'
        };
    }

    getLastPostId() {
        const posts = this.postContainer.querySelectorAll('.Post');
        if (posts.length === 0) return null;
        
        const lastPost = posts[posts.length - 1];
        return lastPost.dataset.postId;
    }

    handleScroll() {
        if (this.isLoading || !this.hasMorePosts) return;

        const scrollTop = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const offset = windowHeight * 0.1;

        // When the bottom of the window is within 'offset' pixels of the bottom of the document, load more posts
        if (scrollTop + windowHeight >= documentHeight - offset) {
            this.loadMorePosts();
        }
    }

    async loadMorePosts() {
        if (this.isLoading || !this.hasMorePosts) return;

        this.isLoading = true;
        this.lastPostId = this.getLastPostId();

        try {
            const params = {
                lastPostId: this.lastPostId || (this.filters.sortOrder === 'asc' ? '0' : '2147483647'),
                ...this.filters
            };

            const posts = await fetchPosts(params);

            if (posts.length === 0) {
                this.hasMorePosts = false;
                return;
            }

            this.appendPosts(posts);
        } catch (error) {
            console.error('Error loading more posts:', error);
        } finally {
            this.isLoading = false;
        }
    }

    appendPosts(posts) {
        posts.forEach(post => {
            const postElement = this.createPostElement(post);
            this.postContainer.appendChild(postElement);
            // Setup listeners on the new post
            this.postManager.setupPostListeners(postElement);
        });
    }

    createPostElement(post) {
        const template = document.getElementById('post-template');
        const article = template.content.cloneNode(true).querySelector('.Post');
        
        // Set data attributes
        article.dataset.postId = post.postId;
        article.dataset.authorId = post.author.userId;
        
        // Set basic fields
        article.querySelector('[data-field="title"]').textContent = post.title;
        article.querySelector('[data-field="author"]').textContent = post.author.userId;
        
        const timeElement = article.querySelector('[data-field="createdAt"]');
        timeElement.textContent = post.createdAt;
        timeElement.setAttribute('datetime', post.createdAt);
        
        article.querySelector('[data-field="description"]').innerHTML = post.description.replace(/\n/g, '<br>');
        
        // Set course
        article.querySelector('[data-field="courseName"]').textContent = post.course.courseName;
        
        const tagsContainer = article.querySelector('[data-field="tags"]');
        
        // Add category if exists
        if (post.category) {
            const categoryLi = tagsContainer.insertAdjacentHTML('beforeend', 
                `<li class="tag type"><a href="#">${this.escapeHtml(post.category.categoryName)}</a></li>`
            );
        }
        
        // Add tags
        if (post.tags && post.tags.length > 0) {
            post.tags.forEach(tag => {
                tagsContainer.insertAdjacentHTML('beforeend',
                    `<li class="tag topic"><a href="#">${this.escapeHtml(tag.tag_name)}</a></li>`
                );
            });
        }
        
        // Add attachment if exists at the beginning of review list
        if (post.attachmentPath) {
            const reviewList = article.querySelector('[data-field="reviewList"]');
            reviewList.insertAdjacentHTML('afterbegin',
                `<li><a href="${this.escapeHtml(post.attachmentPath)}" download>Download Notes</a></li>`
            );
        }
        
        // Set like/dislike counts and states
        const likesData = article.querySelector('[data-field="likes"]');
        likesData.textContent = post.likes;
        likesData.setAttribute('value', post.likes);
        
        const dislikesData = article.querySelector('[data-field="dislikes"]');
        dislikesData.textContent = post.dislikes;
        dislikesData.setAttribute('value', post.dislikes);
        
        if (post.likedByUser === true) {
            article.querySelector('.btn-like').classList.add('active');
        } else if (post.likedByUser === false) {
            article.querySelector('.btn-dislike').classList.add('active');
        }
        
        // Set comments link
        article.querySelector('[data-field="commentsLink"]').href = `/posts/${post.postId}`;
        
        return article;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
