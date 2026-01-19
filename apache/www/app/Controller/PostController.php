<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\routes\Delete;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Auth;
use Unibostu\Model\Service\PostService;
use Unibostu\Model\Service\CommentService;
use Unibostu\Model\Service\CourseService;

class PostController extends BaseController {
    private $postService;
    private $commentService;
    private $courseService;

    #[Get("/posts/:postid")]
    public function getPost(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        if ($postId === null) {
            return new Response('Post ID is required', 400);
        }

        if ($this->getContainer()->get(Auth::class)->isAuthenticatedAsUser() || true) {
            $userId = "mrossi"; //$this->getContainer()->get(Auth::class)->getAuthenticatedUserId();
        } else {
            return new Response('Unauthorized', 401);
        }

        $this->postService = new PostService();
        $this->commentService = new CommentService();
        $this->courseService = new CourseService();
        $this->container->get(Auth::class);
        return $this->render("postcomments", [
            "courses" => $this->courseService->getCoursesByUser($userId),
            "post" => $this->postService->getPostDetails((int)$postId),
            "comments" => $this->commentService->getCommentsByPostId((int)$postId)
        ]);
    }

    #[Post("/posts/:postid/comments/addComment")]
    public function addComment(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        if ($postId === null) {
            return new Response('Post ID is required', 400);
        }
        if ($this->getContainer()->get(Auth::class)->isAuthenticatedAsUser() || true) {
            $userId = "mrossi"; //$this->getContainer()->get(Auth::class)->getAuthenticatedUserId();
        } else {
            return new Response('Unauthorized', 401);
        }
        $commentText = $request->post('comment-text') ?? null;
        if ($commentText === null || trim($commentText) === '') {
            return new Response('Comment text is required', 400);
        }
        $this->commentService = new CommentService();
        $this->commentService->createComment(new \Unibostu\Model\DTO\CreateCommentDTO(
            postId: (int)$postId,
            userId: $userId,
            text: $commentText
        ));

        return Response::create()->redirect("/posts/" . htmlspecialchars($postId));
    }

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
