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
use Unibostu\Model\Service\AttachmentService;


class PostController extends BaseController {
    private PostService $postService;
    private CommentService $commentService;
    private CourseService $courseService;
    private CategoryService $categoryService;
    private TagService $tagService;
    private UserService $userService;
    private AttachmentService $attachmentService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->commentService = new CommentService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->tagService = new TagService();
        $this->userService = new UserService();
        $this->attachmentService = new AttachmentService();
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
    ], ["categoryId", "tags"])]
    public function createPost(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $fields = $request->getAttribute(RequestAttribute::FIELDS);
        $title = $fields['title'];
        $description = $fields['description'];
        $courseId = $fields['courseId'];
        $categoryId = $fields['categoryId'];
        $tagIds = $fields['tags'];
        // Parse tags
        $tags = [];
        if (!empty($tagIds) && is_array($tagIds)) {
            $tags = array_map(function($tagId) use ($courseId) {
                return [
                    'tagId' => (int)$tagId,
                    'courseId' => (int)$courseId
                ];
            }, $tagIds);
        }
        // Validate files BEFORE creating the post
        $hasFiles = isset($_FILES['files']) && !empty($_FILES['files']['name'][0]);
        if ($hasFiles) {
            $this->attachmentService->validateFiles($_FILES['files']);
        }
        // Create the post
        $createPostDTO = new CreatePostDTO(
            userId: $userId,
            courseId: (int)$courseId,
            title: $title,
            description: $description,
            tags: $tags,
            category: !empty($categoryId) ? (int)$categoryId : null
        );
        $postId = $this->postService->createPost($createPostDTO);
        
        // Process file uploads (already validated)
        $attachments = [];
        if ($hasFiles) {
            $attachments = $this->attachmentService->processUploadedFiles($postId, $_FILES['files']);
        }
        
        return Response::create()->json([
            'success' => true,
            'redirect' => '/courses/' . $courseId,
            'attachments' => array_map(fn($a) => $a->toArray(), $attachments)
        ]);
    }

    /**
     * Serve attachment files
     */
    #[Get("/api/attachments/:filename")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function serveAttachment(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $filename = $pathVars['filename'];
        // Validate filename for security
        if (empty($filename) || preg_match('/[^a-zA-Z0-9_\-\.]/', $filename) || str_contains($filename, '..')) {
            return Response::create()->withContent("Invalid filename")->withStatusCode(400);
        }
        // Get attachment info from database
        $attachment = $this->attachmentService->getAttachmentByFileName($filename);
        if (!$attachment) {
            return Response::create()->withContent("Attachment not found")->withStatusCode(404);
        }
        // Check file exists on disk
        if (!$this->attachmentService->fileExists($filename)) {
            return Response::create()->withContent("File not found")->withStatusCode(404);
        }
        $filePath = $this->attachmentService->getFilePath($filename);
        $content = file_get_contents($filePath);
        // Return file with proper headers
        return Response::create()
            ->withContent($content)
            ->withHeader('Content-Type', $attachment->mimeType)
            ->withHeader('Content-Length', (string)$attachment->fileSize)
            ->withHeader('Content-Disposition', 'inline; filename="' . $attachment->originalName . '"');
    }

    #[Delete("/api/posts/:postid")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    #[ValidationMiddleware()]
    public function deletePost(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;

        try {
            $this->postService->deletePost((int)$postId, $userId, $isAdmin);
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
    
    #[Post("/api/posts/:postid/reaction")]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function toggleReaction(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $postId = $pathVars['postid'] ?? null;
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $action = $request->post('action');

        if ($action === 'like') {
            $result = $this->postService->toggleLike((int)$postId, $userId);
        } elseif ($action === 'dislike') {
            $result = $this->postService->toggleDislike((int)$postId, $userId);
        } else {
            ValidationException::build()->addError(ValidationErrorCode::INVALID_REQUEST)->throwIfAny();
        }
        
        return Response::create()->json([
            'success' => true,
            'data' => $result
        ]);
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
                'tags' => $post->tags,
                'attachments' => array_map(function($att) {
                    return $att->toArray();
                }, $post->attachments)
            ];
        }, $posts);

        return Response::create()->json([
            'success' => true,
            'data' => $postsArray
        ]);
    }
}
