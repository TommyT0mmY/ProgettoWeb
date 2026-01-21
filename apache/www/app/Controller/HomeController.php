<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Exception;
use Unibostu\Core\Container;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
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
    public function getHomepagePosts(array $params, Request $request): Response {
        $postQuery = null; 
        $userId = null;
        if ($this->getAuth()->isAuthenticatedAsAdmin()) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);                                  //|| true per testing poi lo tolgo
        } else if ($this->getAuth()->isAuthenticatedAsUser()) {
            $userId = $this->getAuth()->getUserId();
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->inCategory($request->get('categoryId'))
                ->withTags($request->get('tags'))
                ->sortedBy($request->get('sortOrder'));
        } else {
            throw new Exception('You are not authenticated');
        }

        return $this->render("home", [
            "posts" => $this->postService->getPosts($postQuery),
            "courses" => $this->courseService->getCoursesByUser($userId),
            "categories" => $this->categoryService->getAllCategories()
        ]);
    }
}

