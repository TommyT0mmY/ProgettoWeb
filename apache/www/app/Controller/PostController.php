<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\routes\Delete;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Role;
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
    public function getPost(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postId = $params['postid'] ?? null;
        if ($postId === null) {
            return new Response('Post ID is required', 400);
        }

        if ($this->getAuth()->isAuthenticated(Role::USER)) {
            $userId = $this->getAuth()->getId(Role::USER);
        } else {
            return new Response('Unauthorized', 401);
        }

        return $this->render("postcomments", [
            "courses" => $this->courseService->getCoursesByUser($userId),
            "post" => $this->postService->getPostDetails((int)$postId),
            "comments" => $this->commentService->getCommentsByPostId((int)$postId),
            "userId" => $userId,
        ]);
    }

    #[Post("/api/posts/:postid/comments")]
    public function addComment(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postId = $params['postid'] ?? null;
        if ($postId === null) {
            return new Response('Post ID is required', 402);
        }
        if ($this->getAuth()->isAuthenticated(Role::USER)) {
            $userId = $this->getAuth()->getId(Role::USER);
        } else {
            return new Response('Unauthorized', 401);
        }

        $text = $request->post("text");
        if ($text === null || trim($text) === '') {
            return new Response('Comment text is required', 403);
        }

        $parentCommentId = $request->post("parentCommentId");
        if (isset($parentCommentId)) {
            $parentCommentId = (int)$parentCommentId;
        } else {
            $parentCommentId = null;
        }

        $commentWithAuthor = $this->commentService->createComment(new CreateCommentDTO(
            postId: (int)$postId,
            userId: $userId,
            text: $text,
            parentCommentId: $parentCommentId
        ));

        return new Response(json_encode([
            'commentId' => $commentWithAuthor->commentId,
            'postId' => $commentWithAuthor->postId,
            'text' => $commentWithAuthor->text,
            'createdAt' => $commentWithAuthor->createdAt,
            'deleted' => $commentWithAuthor->deleted,
            'parentCommentId' => $commentWithAuthor->parentCommentId,
            'author' => [
                'userId' => $commentWithAuthor->author->userId,
                'firstName' => $commentWithAuthor->author->firstName,
                'lastName' => $commentWithAuthor->author->lastName,
            ]
        ]), 201, ['Content-Type' => 'application/json']);
    }

    #[Post("/api/posts/create")]
    public function createPost(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        return new Response();
    }

    #[Post("/api/posts/search")]
    public function searchPost(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        return new Response();
    }

    #[Get("/api/posts/:postid/comments")]
    public function showComments(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postId = $params['postid'] ?? null;
        $comments = $this->commentService->getCommentsByPostId((int)$postId);

        $commentsArray = array_map(function($commentDTO) {
                return [
                    'commentId' => $commentDTO->commentId,
                    'postId' => $commentDTO->postId,
                    'text' => $commentDTO->text,
                    'createdAt' => $commentDTO->createdAt,
                    'deleted' => $commentDTO->deleted,
                    'parentCommentId' => $commentDTO->parentCommentId,
                    'author' => [
                        'userId' => $commentDTO->author->userId,
                        'firstName' => $commentDTO->author->firstName,
                        'lastName' => $commentDTO->author->lastName,
                    ]
                ];
            }, $comments);

        return new Response(json_encode($commentsArray), 200, ['Content-Type' => 'application/json']);
    }

    #[Delete("/api/posts/:postid")]
    public function deletePost(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postId = $params['postid'] ?? null;
        $userId = $this->getAuth()->getId(Role::USER);
        $this->postService->deletePost((int)$postId, $userId);
        return new Response('Post deleted', 200);
    }

    #[Delete("/api/posts/:postid/comments/:commentid")]
    public function deleteComment(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postId = $params['postid'] ?? null;
        $commentId = $params['commentid'] ?? null;

        if ($postId === null || $commentId === null) {
            return new Response('Post ID and Comment ID are required', 400);
        }

        if ($this->getAuth()->isAuthenticated(Role::USER)) {
            $userId = $this->getAuth()->getId(Role::USER);
        } else {
            return new Response('Unauthorized', 401);
        }
        
        $this->commentService->deleteComment((int)$commentId, (int)$postId, $userId);
        return new Response(json_encode(['text' => 'Comment deleted']), 200);
    }

    #[Post("/api/posts/:postid/like")]
    public function likePost(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postId = $params['postid'] ?? null;
        return new Response();
    }
}
