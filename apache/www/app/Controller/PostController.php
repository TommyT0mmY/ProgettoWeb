<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\routes\Delete;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Model\DTO\CreateCommentDTO; 
use Unibostu\Model\Service\PostService;
use Unibostu\Model\Service\CommentService;
use Unibostu\Model\Service\CourseService;

class PostController extends BaseController {
    private $postService;
    private $commentService;
    private $courseService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->commentService = new CommentService();
        $this->courseService = new CourseService();
    }

    #[Get("/posts/:postid")]
    public function getPost(array $params, Request $request): Response {
        $postId = $params['postid'] ?? null;
        if ($postId === null) {
            return new Response('Post ID is required', 400);
        }

        if ($this->getAuth()->isAuthenticatedAsUser() || true) {
            $userId = "mrossi"; //$this->getAuth()->getAuthenticatedUserId();
        } else {
            return new Response('Unauthorized', 401);
        }

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
        if ($this->getAuth()->isAuthenticatedAsUser() || true) {
            $userId = "mrossi"; //$this->getAuth()->getAuthenticatedUserId();
        } else {
            return new Response('Unauthorized', 401);
        }
        $commentText = $request->post('comment-text') ?? null;
        if ($commentText === null || trim($commentText) === '') {
            return new Response('Comment text is required', 400);
        }

        $this->commentService->createComment(new CreateCommentDTO(
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
