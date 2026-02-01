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
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;
        $selectedCategoryId = $request->get('categoryId');
        $selectedSortOrder = $request->get('sortOrder') ?? 'desc';
        
        // Base query
        $postQuery = PostQuery::create()
            ->forAdmin($isAdmin)
            ->inCategory($selectedCategoryId)
            ->sortedBy($selectedSortOrder);
        
        if (!$isAdmin) {
            $postQuery->forUser($userId);
        }

        $posts = $this->postService->getPosts($postQuery);
        if ($currentRole === Role::ADMIN) {
            return $this->render("admin-home", [
                "posts" => $posts,
                "categories" => $this->categoryService->getAllCategories(),
                "userId" => $userId,
                "sortOrder" => $postQuery->getSortOrder(),
                "categoryId" => $postQuery->getCategory()
            ]);
        } else {
            return $this->render("home", [
                "posts" => $posts,
                "courses" => $this->courseService->getCoursesByUser($userId),
                "categories" => $this->categoryService->getAllCategories(),
                "userId" => $userId,
                "sortOrder" => $postQuery->getSortOrder(),
                "categoryId" => $postQuery->getCategory(),
                "selectedCategoryId" => $selectedCategoryId,
                "selectedSortOrder" => $selectedSortOrder,
                "isAdmin" => $isAdmin
            ]);
        }
    }
}

