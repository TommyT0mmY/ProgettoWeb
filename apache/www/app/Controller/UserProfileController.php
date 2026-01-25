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
use Unibostu\Model\Service\PostService;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\FacultyService;


class UserProfileController extends BaseController {
    private $postService;
    private $courseService;
    private $userService;
    private $facultyService;
    private $categoryService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
        $this->categoryService = new CategoryService();
    }

    /** get user profile */
    #[Get('/user-profile/:userid')]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getUserProfilePosts(Request $request): Response {
        $postQuery = null;
        $userId = null; //per testing usare "laura.monti"

        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        if ($currentRole === Role::ADMIN) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);                                  
        } else if ($currentRole === Role::USER) {
            $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->authoredBy($userId)
                ->inCategory($request->get('categoryId'))
                ->withTags($request->get('tags'))
                ->sortedBy($request->get('sortOrder'));
        }
        $user = $this->userService->getUserProfile($userId);

        return $this->render("user-profile", [
            'user' => $user,
            'posts' => $this->postService->getPosts($postQuery),
            'courses' => $this->courseService->getCoursesByUser($userId),
            'faculty' => $this->facultyService->getFacultyDetails($user->facultyId),
            'categories' => $this->categoryService->getAllCategories()
        ]);
    }
}

