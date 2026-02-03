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
use Unibostu\Model\Service\CommentService;

class CommentController extends BaseController {
    private CommentService $commentService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->commentService = new CommentService();
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

    #[Get("/api/posts/:postid/comments")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
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
    
    #[Delete("/api/posts/:postid/comments/:commentid")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
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
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;
        $this->commentService->deleteComment((int)$commentId, (int)$postId, $userId, $isAdmin);
        return Response::create()->json([
            'success' => true
        ]);
    }
}

