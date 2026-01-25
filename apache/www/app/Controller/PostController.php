<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\middleware\ValidationMiddleware;
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

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->commentService = new CommentService();
        $this->courseService = new CourseService();
    }

    #[Get("/posts/:postid")]
    #[AuthMiddleware(Role::USER)]
    public function getPost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        if ($postId === null) {
            throw new ValidationException(errors: [ValidationErrorCode::POST_ID_REQUIRED]);
        }
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        return $this->render("postcomments", [
            "courses" => $this->courseService->getCoursesByUser($userId),
            "post" => $this->postService->getPostDetails((int)$postId),
            "comments" => $this->commentService->getCommentsByPostId((int)$postId),
            "userId" => $userId,
        ]);
    }

    #[Post("/api/posts/:postid/comments")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function addComment(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        if ($postId === null) {
            throw new ValidationException(errors: [ValidationErrorCode::POST_ID_REQUIRED]);
        }
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $text = $request->post("text");
        if ($text === null || trim($text) === '') {
            throw new ValidationException(errors: [ValidationErrorCode::COMMENT_TEXT_REQUIRED]);
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
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function createPost(Request $request): Response {
        return new Response();
    }

    #[Post("/api/posts/search")]
    #[AuthMiddleware(Role::USER)]
    public function searchPost(Request $request): Response {
        return new Response();
    }

    #[Get("/api/posts/:postid/comments")]
    #[AuthMiddleware(Role::USER)]
    public function showComments(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
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
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function deletePost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $this->postService->deletePost((int)$postId, $userId);
        return new Response('Post deleted', 200); // TODO JSON
    }

    #[Delete("/api/posts/:postid/comments/:commentid")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function deleteComment(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $commentId = $pathVars['commentid'] ?? null;
        if ($postId === null) {
            throw new ValidationException(errors: [ValidationErrorCode::POST_ID_REQUIRED]);
        }
        if ($commentId === null) {
            throw new ValidationException(errors: [ValidationErrorCode::COMMENT_ID_REQUIRED]);
        }
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $this->commentService->deleteComment((int)$commentId, (int)$postId, $userId);
        return new Response(json_encode(['text' => 'Comment deleted']), 200);
    }

    #[Post("/api/posts/:postid/like")]
    #[AuthMiddleware(Role::USER)]
    public function likePost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        return new Response();
    }
}
