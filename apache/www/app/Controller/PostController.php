<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\routes\Delete;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;

class PostController extends BaseController {
    #[Post("/api/posts/create")]
    public function createPost(array $params, Request $request): Response {
        return new Response();
    }

    #[Post("/api/posts/search")]
    public function searchPost(array $params, Request $request): Response {
        return new Response();
    }

    #[Get("/api/posts/:postid/comments")]
    public function showComments(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }

    #[Post("/api/posts/:postid/comments")]
    public function addComment(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }

    #[Delete("/api/posts/:postid")]
    public function deletePost(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }

    #[Delete("/api/posts/:postid/comments/:commentid")]
    public function deleteComment(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        $commentId = $params['commentid'] ?? null;
        return new Response();
    }

    #[Post("/api/posts/:postid/like")]
    public function likePost(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }
}
