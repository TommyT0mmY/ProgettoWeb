import { PostManager } from './PostManager.js';
import { InfiniteScroll } from './infinitescroll.js';

document.addEventListener('DOMContentLoaded', () => {
    const userId = window.currentUser || null;
    const postManager = new PostManager(userId);
    new InfiniteScroll(postManager);
});
