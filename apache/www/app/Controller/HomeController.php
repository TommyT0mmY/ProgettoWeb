<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\PostService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;

class HomeController extends BaseController {
    private $postService;
    private $courseService;
    private $categoryService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
    }

    #[Get('/')]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getHomepagePosts(Request $request): Response {
        $postQuery = null; 
        $userId = null;
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        if ($currentRole === Role::ADMIN) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);
        } else if ($currentRole === Role::USER) {
            $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->inCategory($request->get('categoryId'))
                ->sortedBy($request->get('sortOrder'));
        }

        $posts = $this->postService->getPosts($postQuery);

        return $this->render("home", [
            "posts" => $posts,
            "courses" => $this->courseService->getCoursesByUser($userId),
            "categories" => $this->categoryService->getAllCategories(),
            "userId" => $userId,
            "sortOrder" => $postQuery->getSortOrder(),
            "categoryId" => $postQuery->getCategory()
        ]);
    }

    #[Get('/api/posts')]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getHomepagePostsApi(Request $request): Response {
        $postQuery = null;
        $userId = null;
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        
        if ($currentRole === Role::ADMIN) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);
        } else if ($currentRole === Role::USER) {
            $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->inCategory($request->get('categoryId'))
                ->sortedBy($request->get('sortOrder'))
                ->afterPost($request->get('lastPostId') ?? ($request->get('sortOrder') === 'asc' ? 0 : PHP_INT_MAX));
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

