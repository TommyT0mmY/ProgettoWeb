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
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Model\DTO\CreatePostDTO;
use Unibostu\Model\Service\PostService;
use Unibostu\Model\Service\CommentService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\TagService;
use Unibostu\Model\Service\UserService;


class PostController extends BaseController {
    private $postService;
    private $commentService;
    private $courseService;
    private $categoryService;
    private $tagService;
    private $userService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->commentService = new CommentService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->tagService = new TagService();
        $this->userService = new UserService();
    }

    #[Get('/courses/:courseId/createpost')]
    #[AuthMiddleware(Role::USER)]
    public function createPosts(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $courseId = $pathVars['courseId'];

        $userId = $request->getAttribute(RequestAttribute::ROLE_ID); 
        $user = $this->userService->getUserProfile($userId);
        
        return $this->render("createpost", [
            "userId" => $userId,
            "courses" => $this->courseService->getCoursesByUser($userId),
            "thisCourse" => $this->courseService->getCourseDetails((int)$courseId),
            "categories" => $this->categoryService->getAllCategories(),
            "tags" => $this->tagService->getTagsByCourse((int)$courseId)             
        ]);
    }   

    #[Get("/posts/:postid")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getPost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        if ($postId === null) {
            throw new ValidationException(errors: [ValidationErrorCode::POST_ID_REQUIRED]);
        }
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;

        $viewParams = [
            "post" => $this->postService->getPostDetails((int)$postId),
            "comments" => $this->commentService->getCommentsByPostId((int)$postId),
            "userId" => $userId,
            "isAdmin" => $isAdmin
        ];

        // Add courses only for non-admin users
        if (!$isAdmin) {
            $viewParams["courses"] = $this->courseService->getCoursesByUser($userId);
        }

        return $this->render("postcomments", $viewParams);
    }

    #[Post("/api/posts/create")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware([
        "title" => ValidationErrorCode::TITLE_REQUIRED,
        "description" => ValidationErrorCode::DESCRIPTION_REQUIRED,
        "courseId" => ValidationErrorCode::COURSE_REQUIRED
    ], ["categoryId", "tags", "file"])]
    public function createPost(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $fields = $request->getAttribute(RequestAttribute::FIELDS);
        
        // Parse tags
        $tags = [];
        if (isset($fields['tags']) && is_array($fields['tags'])) {
            $tags = array_map(function($tagId) use ($fields) {
                return [
                    'tagId' => (int)$tagId,
                    'courseId' => (int)$fields['courseId']
                ];
            }, $fields['tags']);
        }
        
        // Handle file upload
        $attachmentPath = null;
        // TODO: Implement file upload handling
        
        $createPostDTO = new CreatePostDTO(
            userId: $userId,
            courseId: (int)$fields['courseId'],
            title: $fields['title'],
            description: $fields['description'],
            tags: $tags,
            category: isset($fields['categoryId']) && $fields['categoryId'] !== '' ? (int)$fields['categoryId'] : null,
            attachmentPath: $attachmentPath
        );
        
        $this->postService->createPost($createPostDTO);
        
        return Response::create()->json([
            'success' => true,
            'redirect' => '/courses/' . $fields['courseId']
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

    #[Get('/api/posts')]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getPostsApi(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;
        
        // Parse tags from GET parameters
        $tags = [];
        $tagIds = $request->get('tags');
        $courseId = $request->get('courseId');
        if (is_array($tagIds) && !empty($courseId)) {
            foreach ($tagIds as $tagId) {
                $tags[] = [
                    'tagId' => (int)$tagId,
                    'courseId' => (int)$courseId
                ];
            }
        }
        
        // Base query
        $postQuery = PostQuery::create()
            ->forAdmin($isAdmin)
            ->inCategory($request->get('categoryId'))
            ->sortedBy($request->get('sortOrder'))
            ->afterPost($request->get('lastPostId') ?? ($request->get('sortOrder') === 'asc' ? 0 : PHP_INT_MAX));

        if (str_contains($request->getReferer(), '/courses')) {
            $postQuery
                ->inCourse($courseId)
                ->withTags($tags);
        } else if (str_contains($request->getReferer(), '/users')) {
            $postQuery->authoredBy($request->get('authorId'));
        } else {
            // Homepage: User sees only posts of the courses they are enrolled in, Admin sees all posts
            if (!$isAdmin) {
                $postQuery->forUser($userId);
            }
        }

        $posts = $this->postService->getPosts($postQuery);
        
        $postsArray = array_map(function($post) {
            return [
                'postId' => $post->postId,
                'title' => $post->title,
                'description' => $post->description,
                'createdAt' => $post->createdAt,
                'attachmentPath' => $post->attachmentPath,
                'likes' => $post->likes,
                'dislikes' => $post->dislikes,
                'likedByUser' => $post->likedByUser,
                'author' => [
                    'userId' => $post->author->userId,
                    'firstName' => $post->author->firstName,
                    'lastName' => $post->author->lastName
                ],
                'course' => [
                    'courseId' => $post->course->courseId,
                    'courseName' => $post->course->courseName
                ],
                'category' => $post->category ? [
                    'categoryId' => $post->category->categoryId,
                    'categoryName' => $post->category->categoryName
                ] : null,
                'tags' => $post->tags
            ];
        }, $posts);

        return Response::create()->json([
            'success' => true,
            'data' => $postsArray
        ]);
    }
}
