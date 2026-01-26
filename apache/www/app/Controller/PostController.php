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
    #[ValidationMiddleware([
        "text" => ValidationErrorCode::COMMENT_TEXT_REQUIRED
    ], ["parentCommentId"])]
    public function addComment(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        if ($postId === null) {
            throw new ValidationException(errors: [ValidationErrorCode::POST_ID_REQUIRED]);
        }
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        [ "text" => $text, "parentCommentId" => $parentCommentId ] = $request->getAttribute(RequestAttribute::FIELDS);
        $parentCommentId = isset($parentCommentId) ? (int)$parentCommentId : null;
        $commentWithAuthor = $this->commentService->createComment(new CreateCommentDTO(
            postId: (int)$postId,
            userId: $userId,
            text: $text,
            parentCommentId: $parentCommentId
        ));
        return Response::create()->json([
            'success' => true,
            'data' => [
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
            ]
        ], 201);
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
        return Response::create()->json([
            'success' => true,
            'data' => $commentsArray
        ]);
    }

    #[Delete("/api/posts/:postid")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function deletePost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);

        try {
            $this->postService->deletePost((int)$postId, $userId);
            if ($request->getReferer() === null || str_contains($request->getReferer(), '/posts/')) {
                // If the request comes from the post detail page, redirect to home
                return Response::create()->json([
                    'success' => true,
                    'redirect' => '/'
                ]);
            } else {
                return Response::create()->json([
                    'success' => true,
                    'redirect' => $request->getReferer()
                ]);
            }
        } catch (\Exception $e) {
            return Response::create()->json([
                'success' => false,
                'errors' => [$e->getMessage()]
            ], 403);
        }
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
        return Response::create()->json([
            'success' => true,
            'message' => 'Comment deleted'
        ]);
    }

    #[Post("/api/posts/:postid/like")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function likePost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);

        try {
            $result = $this->postService->toggleLike((int)$postId, $userId);
            return Response::create()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return Response::create()->json([
                'success' => false,
                'errors' => [$e->getMessage()]
            ], 400);
        }
    }

    #[Post("/api/posts/:postid/dislike")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function dislikePost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);

        try {
            $result = $this->postService->toggleDislike((int)$postId, $userId);
            return Response::create()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return Response::create()->json([
                'success' => false,
                'errors' => [$e->getMessage()]
            ], 400);
        }
    }
}
