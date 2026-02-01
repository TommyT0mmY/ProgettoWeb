import { PostManager } from './PostManager.js';
import { InfiniteScroll } from './infinitescroll.js';

document.addEventListener('DOMContentLoaded', () => {
    const userId = window.currentUser || null;
    const isAdmin = window.isAdmin || false;
    const postManager = new PostManager(userId, isAdmin);
    new InfiniteScroll(postManager);
});
