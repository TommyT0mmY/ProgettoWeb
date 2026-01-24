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
    public function index(): Response {
        return $this->render("home", []);
    }

    #[Get('/homepage')]
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
                ->withTags($request->get('tags'))
                ->sortedBy($request->get('sortOrder'));
        }

        return $this->render("home", [
            "posts" => $this->postService->getPosts($postQuery),
            "courses" => $this->courseService->getCoursesByUser($userId),
            "categories" => $this->categoryService->getAllCategories()
        ]);
    }
}

