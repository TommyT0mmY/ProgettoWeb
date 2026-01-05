<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;

class PostController extends BaseController {

    public function createPost(array $params, Request $request): Response {
        return new Response();
    }

    public function searchPost(array $params, Request $request): Response {
        return new Response();
    }

    public function showComments(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }

    public function addComment(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }

    public function deletePost(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }

    public function deleteComment(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        $commentId = $params['commentid'] ?? null;
        return new Response();
    }

    public function likePost(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        return new Response();
    }
}
