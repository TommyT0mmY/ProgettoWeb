const API_BASE = 'http://localhost:80/api';

export async function fetchComments(postId) {
    try {
        const response = await fetch(`${API_BASE}/posts/${postId}/comments`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        if (!result.success) {
            throw new Error(result.errors ? result.errors.join(', ') : 'Failed to fetch comments');
        }
        
        return result.data;
    } catch (error) {
        console.error('Error fetching comments:', error);
        throw error;
    }
}

export async function postComment(commentData) {
    try {
        const response = await fetch(`${API_BASE}/posts/${commentData.postid}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(commentData)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.errors ? result.errors.join(', ') : 'Failed to post comment');
        }
        
        return result.data;
    } catch (error) {
        console.error('Error posting comment:', error);
        throw error;
    }
}

export async function deleteComment(postId, commentId) {
    try {
        const response = await fetch(`${API_BASE}/posts/${postId}/comments/${commentId}`, {
            method: 'DELETE'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.errors ? result.errors.join(', ') : 'Failed to delete comment');
        }
        
        return result;
    } catch (error) {
        console.error('Error deleting comment:', error);
        throw error;
    }
}