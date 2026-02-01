const API_BASE = '/api';

export async function fetchPosts(params = {}) {
    try {
        const queryParams = new URLSearchParams(params);
        const response = await fetch(`${API_BASE}/posts?${queryParams.toString()}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        if (!result.success) {
            throw new Error(result.errors ? result.errors.join(', ') : 'Failed to fetch posts');
        }
        
        return result.data;
    } catch (error) {
        console.error('Error fetching posts:', error);
        throw error;
    }
}

export async function deletePost(postId) {
    try {
        const body = {
            'csrf-key': window.csrfKey,
            'csrf-token': window.csrfToken
        };
        
        const response = await fetch(`${API_BASE}/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.errors ? result.errors.join(', ') : 'Failed to delete post');
        }
        
        return result;
    } catch (error) {
        console.error('Error deleting post:', error);
        throw error;
    }
}

export async function toggleReaction(postId, action) {
    try {
        const body = {
            'csrf-key': window.csrfKey,
            'csrf-token': window.csrfToken,
            'action': action
        };
        
        const response = await fetch(`${API_BASE}/posts/${postId}/reaction`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.errors ? result.errors.join(', ') : 'Failed to toggle reaction');
        }
        
        return result.data;
    } catch (error) {
        console.error('Error toggling reaction:', error);
        throw error;
    }
}

export async function likePost(postId) {
    return toggleReaction(postId, 'like');
}

export async function dislikePost(postId) {
    return toggleReaction(postId, 'dislike');
}
