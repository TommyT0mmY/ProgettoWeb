<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Exception;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Model\Service\PostService;
use Unibostu\Core\security\Auth;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;

class HomeController extends BaseController {
    private $postService;
    private $courseService;
    private $categoryService;

    #[Get('/')]
    public function index(): Response {
        return $this->render("home", []);
    }

    #[Get('/homepage')]
    public function getHomepagePosts(array $params, Request $request): Response {
        $postQuery = null; 
        $userId = "mrossi"; //$this->getContainer()->get(Auth::class)->getAuthenticatedUserId();                             
        if ($this->getContainer()->get(Auth::class)->isAuthenticatedAsAdmin()) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);                                             //|| true per testing poi lo tolgo
        } else if ($this->getContainer()->get(Auth::class)->isAuthenticatedAsUser() || true) {
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->inCategory($request->get('categoryId'))
                ->withTags($request->get('tags'))
                ->sortedBy($request->get('sortOrder'));
        } else {
            throw new Exception('You are not authenticated');
        }

        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        return $this->render("home", [
            "posts" => $this->postService->getPosts($postQuery),
            "courses" => $this->courseService->getCoursesByUser($userId),
            "categories" => $this->categoryService->getAllCategories()
        ]);
    }
}

